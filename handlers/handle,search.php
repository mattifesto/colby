<?php

global $searchQueryHTML;

$searchQuery = cb_query_string_value('search-for');
$searchQueryHTML = cbhtml($searchQuery);
$title = 'Search';

if ($searchQueryHTML) {
    $title = "{$title}: {$searchQuery}";
}

CBHTMLOutput::begin();
CBHTMLOutput::pageInformation()->title = $title;
CBHTMLOutput::pageInformation()->description = 'Search for site content.';
CBHTMLOutput::addCSSURL(cbsysurl() . '/handlers/handle,search.css');

CBPageLayout::renderPageHeader();

$formClass = empty($searchQuery) ? 'no-query' : 'has-query';

?>

<main style="flex: 1 1 auto;">
<form action="<?= cbsiteurl() ?>/search/" class="search <?= $formClass ?>">
    <div>
        <input type="text" name="search-for" value="<?php echo $searchQueryHTML; ?>">
        <input type="submit" value="Search Now">
    </div>
</form>

<?php

if (empty($searchQuery)) {
    goto done;
}

?>

<section class="CBPageSearchResultsView">

    <?php

    $searchQueryAsSQL = Colby::mysqli()->escape_string($searchQuery);
    $searchQueryAsSQL = "'%$searchQueryAsSQL%'";
    $SQL = <<<END

        SELECT      LOWER(HEX(`archiveId`))
        FROM        `ColbyPages`
        WHERE       `published` IS NOT NULL AND
                    `searchText` LIKE {$searchQueryAsSQL}
        ORDER BY    `published`

END;

    $IDs = CBDB::SQLToArray($SQL);
    $models = CBModels::fetchModelsByID($IDs);

    if (count($models) > 0) {
        foreach ($models as $model) {
            $URI = CBConvert::valueToString(CBModel::value($model, 'URI'));
            $URL = CBSitePreferences::siteURL() . "/{$URI}/";
            $imageURL = CBImage::valueToFlexpath($model, 'image', 'rw320', cbsiteurl());
            $title = CBConvert::valueToString(CBModel::value($model, 'title'));
            $description = CBConvert::valueToString(CBModel::value($model, 'description'));

            ?>

            <article class="result">
                <?php

                CBArtworkElement::render([
                    'height' => 100,
                    'width' => 100,
                    'maxWidth' => '100px',
                    'URL' => $imageURL,
                ]);

                ?>
                <div class="text">
                    <h1><a href="<?= $URL ?>"><?= cbhtml($title) ?></a></h1>
                    <div><p><?= cbhtml($description) ?></div>
                </div>
            </article>

            <?php
        }
    }

    ?>

</section>

<?php

done:

echo '</main>';

CBPageLayout::renderPageFooter();

CBHTMLOutput::render();

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
CBHTMLOutput::addCSSURL(cbsysurl() . '/handlers/handle,search.v459.css');

CBPageLayout::renderPageHeader();

$formClass = empty($searchQuery) ? 'no-query' : 'has-query';

?>

<main class="CBSearch">
    <form action="<?= cbsiteurl() ?>/search/" class="search <?= $formClass ?>">
        <div>
            <input
                type="text"
                name="search-for"
                value="<?php echo $searchQueryHTML; ?>"
            >
            <input
                type="submit"
                value="Search Now"
            >
        </div>
    </form>

    <?php

    if (empty($searchQuery)) {
        goto done;
    }

    ?>

    <section class="CBPageSearchResultsView">

        <?php

        $searchClause = CBPages::searchClauseFromString(
            $searchQuery
        );

        if (empty($searchClause)) {
            $summaries = [];
        } else {
            $SQL = <<<END

                SELECT      keyValueData
                FROM        ColbyPages
                WHERE       published IS NOT NULL AND
                            {$searchClause}
                ORDER BY    published

            END;

            $summaries = CBDB::SQLToArray(
                $SQL,
                [
                    'valueIsJSON' => true,
                ]
            );
        }

        if (count($summaries) > 0) {
            foreach ($summaries as $model) {
                $URI = CBModel::valueToString(
                    $model,
                    'URI'
                );

                $URL = cbsiteurl() . "/{$URI}/";

                $title = CBModel::valueToString(
                    $model,
                    'title'
                );

                $description = CBModel::valueToString(
                    $model,
                    'description'
                );

                $imageURL = CBImage::valueToFlexpath(
                    $model,
                    'image',
                    'rl320',
                    cbsiteurl()
                );

                if (empty($imageURL)) {
                    $imageURL = CBModel::valueToString(
                        $model,
                        'thumbnailURL'
                    );
                }

                ?>

                <article class="CBSearchResultView result">
                    <div class="CBSearchResultView_thumbnail">
                        <?php

                        CBArtworkElement::render(
                            [
                                'aspectRatioWidth' => 1,
                                'aspectRatioHeight' => 1,
                                'maxWidth' => '150',
                                'URL' => $imageURL,
                            ]
                        );

                        ?>
                    </div>
                    <div class="text">
                        <h1>
                            <a href="<?= $URL ?>"><?= cbhtml($title) ?></a>
                        </h1>
                        <div>
                            <p><?= cbhtml($description) ?>
                        </div>
                    </div>
                </article>

                <?php
            }
        }

        ?>

    </section>

    <?php

    done:

    ?>

</main>

<?php

CBPageLayout::renderPageFooter();

CBHTMLOutput::render();

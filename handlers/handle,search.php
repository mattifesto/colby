<?php

/**
 * 2014.08.03
 * This file is deprecated. It remains to provide a template for a search page,
 * but this page would only coincidentally be usable on an actual site. This
 * file's presence also prevents sites from not having a search page at this
 * URL. It's not my goal it this moment to remove this file, but its goals
 * should be determined, alternative solutions should be put in place to
 * accomplish those goals, and then the file should be removed.
 */

global $searchQueryHTML;

$searchQuery        = isset($_GET['search-for']) ? $_GET['search-for'] : '';
$searchQueryHTML    = ColbyConvert::textToHTML($searchQuery);
$formClass          = empty($searchQuery) ? 'class="search-page no-query"' : 'class="search-page has-query"';
$titleHTML          = 'Search';

if ($searchQueryHTML) {
    $titleHTML = "{$titleHTML}: {$searchQueryHTML}";
}

CBHTMLOutput::begin();
CBHTMLOutput::setTitleHTML($titleHTML);
CBHTMLOutput::setDescriptionHTML('Search for site content.');

CBPageLayout::renderPageHeader();

?>

<main style="flex: 1 1 auto;">
<form action="<?php echo CBSitePreferences::siteURL(); ?>/search/" <?php echo $formClass; ?>>
    <style>

        form.search-page input[type=text] {
            padding: 5px;
        }

        form.search-page.has-query > div {
            padding: 20px 50px;
            background-color: hsl(0, 0%, 90%);
            border-bottom: 1px solid hsl(0, 0%, 80%);
        }

        form.search-page.has-query input[type=text] {
            width: 500px;
            margin-right: 30px;
        }

        form.search-page.has-query input[type=submit] {
            position: relative;
            top: -3px;
        }

        form.search-page.no-query {
            width: 500px;
            margin: 100px auto;
            text-align: center;
        }

        form.search-page.no-query input[type=text] {
            width: 100%;
            margin-bottom: 30px;
        }

    </style>
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
    <style>
        .CBPageSearchResultsView {
            display: flex;
            flex-wrap: wrap;
            justify-content: center;
        }

        .CBPageSearchResultsView .result {
            box-sizing: border-box;
            display: flex;
            padding: 20px;
            width: 480px;
        }

        .CBPageSearchResultsView .result > div {
            flex: 1 1 auto;
        }

        .CBPageSearchResultsView .result h1 {
            font-size: 1.5em;
        }

        .CBPageSearchResultsView .result > figure {
            flex: none;
            height: 100px;
            margin-right: 10px;
            width: 100px;
        }

        .CBPageSearchResultsView .result > figure img {
            max-width: 100px;
            max-height: 100px;
            box-shadow: 1px 1px 5px rgba(0, 0, 0, 0.5);
        }

        .CBPageSearchResultsView .result > figure div {
            background-color: hsl(30, 30%, 95%);
            height: 100px;
            width: 100px;
        }

    </style>

    <?php

    $sqlQuery = Colby::mysqli()->escape_string($searchQuery);
    $sqlQuery = "'%$sqlQuery%'";

    $sql =<<<END
SELECT
    LOWER(HEX(`archiveId`)) AS `archiveId`,
    `URI`,
    `titleHTML`,
    `subtitleHTML`,
    `thumbnailURL`
FROM
    `ColbyPages`
WHERE
    `published` IS NOT NULL AND
    `searchText` LIKE {$sqlQuery}
ORDER BY
    `className`,
    `classNameForKind`,
    `published`
END;

    $result = Colby::query($sql);

    if ($result->num_rows > 0) {
        while ($row = $result->fetch_object()) {
            $URL = CBSitePreferences::siteURL() . "/{$row->URI}/";

            ?>

            <article class="result">
                <figure>

                    <?php if ($row->thumbnailURL) { ?>

                        <img src="<?php echo $row->thumbnailURL; ?>" alt="<?php $row->titleHTML; ?>">

                    <?php } else { ?>

                        <div></div>

                    <?php } ?>

                </figure>
                <div>
                    <h1><a href="<?php echo $URL; ?>"><?php echo $row->titleHTML; ?></a></h1>
                    <div><p><?php echo $row->subtitleHTML; ?></div>
                </div>
            </article>

            <?php
        }
    }

    $result->free();

    ?>

</section>

<?php

done:

echo '</main>';

CBPageLayout::renderPageFooter();

CBHTMLOutput::render();

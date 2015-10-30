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

include Colby::findFile('sections/standard-page-header.php');

?>

<form action="<?php echo COLBY_SITE_URL; ?>/search/" <?php echo $formClass; ?>>
    <style scoped>

        form.search-page input[type=text] {
            padding: 5px;
        }

        form.search-page.has-query > div {
            padding: 20px 50px;
            background-color: #f7f7f7;
            border-bottom: 1px solid #dddddd;
        }

        form.search-page.has-query input[type=text] {
            width: 500px;
            margin-right: 30px;
        }

        form.search-page.has-query input[type=submit] {
            position: relative;
            top: -3px;
            xbackground-color: #5555ff;
            xcolor: white;
        }

        form.search-page.has-query input[type=submit]:hover {
            xborder: 1px solid #5555aa;
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

<section style="width: 800px; margin: 50px auto 0px;">
    <style scoped>

        article.result {
            padding: 10px;
            overflow: hidden; /* contains floated thumbnail */
            clear: both;
        }

        article.result > h1 {
            font-size: 1.5em;
        }

        div.img {
            width: 100px;
            height: 100px;
            margin-right: 10px;
            float: left;
            text-align: center;
        }

        img.thumbnail {
            max-width: 100px;
            max-height: 100px;
            box-shadow: 1px 1px 5px rgba(0, 0, 0, 0.5);
            vertical-align: top;
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
        while ($row = $result->fetch_object())
        {
            $url = COLBY_SITE_URL . "/{$row->URI}/";

            ?>

            <article class="result">
                <div class="img">

                    <?php

                    if ($row->thumbnailURL) {
                        ?>

                        <img class="thumbnail"
                             src="<?php echo $row->thumbnailURL; ?>"
                             alt="<?php $row->titleHTML; ?>">

                        <?php
                    }

                    ?>

                </div>
                <h1><a href="<?php echo $url; ?>"><?php echo $row->titleHTML; ?></a></h1>
                <div><p><?php echo $row->subtitleHTML; ?></div>
            </article>

            <?php
        }
    }

    $result->free();

    ?>

</section>

<?php

done:

include Colby::findFile('sections/standard-page-footer.php');

CBHTMLOutput::render();

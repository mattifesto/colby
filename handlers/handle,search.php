<?php

// This is where the search query is processed and made available for any
// other included file (such as the header) to use.

global $searchQueryHTML;

$searchQuery = isset($_GET['search-for']) ? $_GET['search-for'] : '';
$searchQueryHTML = ColbyConvert::textToHTML($searchQuery);
$formClass = empty($searchQuery) ? 'class="no-query"' : 'class="has-query"';

$titleHTML = 'Search';

if ($searchQueryHTML)
{
    $titleHTML = "{$titleHTML}: {$searchQueryHTML}";
}

$page = new ColbyOutputManager();

$page->titleHTML = $titleHTML;
$page->descriptionHTML = 'Search for site content.';

$page->begin();

?>

<form action="<?php echo COLBY_SITE_URL; ?>/search/" <?php echo $formClass; ?>>
    <style>
        form input
        {
            padding: 5px;
        }

        form.has-query
        {
            width: 100%;
            padding: 20px 50px;
            background-color: #f7f7f7;
            border-bottom: 1px solid #dddddd;
        }

        form.has-query input[type=text]
        {
            width: 500px;
            margin-right: 30px;
        }

        form.has-query input[type=submit]
        {
            position: relative;
            top: -3px;
            background-color: #5555ff;
            color: white;
        }

        form.has-query input[type=submit]:hover
        {
            border: 1px solid #5555aa;
        }

        form.no-query
        {
            width: 500px;
            margin: 100px auto;
            text-align: center;
        }

        form.no-query input[type=text]
        {
            width: 100%;
            margin-bottom: 30px;
        }
    </style>
    <input type="text" name="search-for" value="<?php echo $searchQueryHTML; ?>">
    <input type="submit" value="Search Now">
</form>

<?php

if (empty($searchQuery))
{
    goto done;
}

?>

<section style="width: 800px; margin: 50px auto 0px;">
    <style>
        article
        {
            margin-bottom: 20px;
            overflow: hidden; /* contains floated thumbnail */
            clear: both;
        }
        article > h1
        {
            font-size: 1.5em;
        }

        article > h2
        {
            font-size: 1.0em;
        }

        div.img
        {
            width: 100px;
            height: 100px;
            margin-right: 10px;
            float: left;
            background-color: #fffff8;
        }

        img.thumbnail
        {
            max-width: 100px;
            max-height: 100px;
        }
    </style>

    <?php

    $sqlQuery = Colby::mysqli()->escape_string($searchQuery);
    $sqlQuery = "'%$sqlQuery%'";

    $sql =<<<END
SELECT
    LOWER(HEX(`archiveId`)) AS `archiveId`,
    LOWER(HEX(`groupId`)) AS `groupId`,
    `stub`,
    `titleHTML`,
    `subtitleHTML`
FROM
    `ColbyPages`
WHERE
    `published` IS NOT NULL AND
    `searchText` LIKE {$sqlQuery}
ORDER BY
    `groupId`,
    `published`
END;

    $result = Colby::query($sql);

    if ($result->num_rows > 0)
    {
        while ($row = $result->fetch_object())
        {
            $url = COLBY_SITE_URL . "/{$row->stub}/";
            $absoluteThumbnailFilename = COLBY_DATA_DIRECTORY . "/{$row->archiveId}/thumbnail.jpg";
            $absoluteThumbnailURL = COLBY_DATA_URL . "/{$row->archiveId}/thumbnail.jpg";

            ?>

            <article>
                <div class="img">

                    <?php

                    if (file_exists($absoluteThumbnailFilename))
                    {
                        ?>

                        <img src="<?php echo $absoluteThumbnailURL; ?>" alt="" class="thumbnail">

                        <?php
                    }

                    ?>

                </div>
                <h1><a href="<?php echo $url; ?>"><?php echo $row->titleHTML; ?></a></h1>
                <h2><?php echo $row->subtitleHTML; ?></h2>
            </article>

            <?php
        }
    }

    $result->free();

    ?>

</section>

<?php

done:

$page->end();

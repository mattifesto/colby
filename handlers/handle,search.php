<?php

$query = isset($_GET['query']) ? $_GET['query'] : null;

$title = 'Search';

if ($query)
{
    $title = "{$title}: {$query}";
}

$page = ColbyOutputManager::beginPage($title, 'Search for site content.');

?>

<form style="text-align: center;">
    <input type="text" name="query" value="<?php echo ColbyConvert::textToHTML($query); ?>">
</form>

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

    $sqlQuery = Colby::mysqli()->escape_string($query);
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

$page->end();

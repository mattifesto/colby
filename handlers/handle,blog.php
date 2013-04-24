<?php

$blogPostsGroupId = '37151457af40ee706cc23de4a11e7ebacafd0c10';

$page = new ColbyOutputManager();

$page->titleHTML = 'Blog';
$page->descriptionHTML = 'Index of blog posts.';

$page->begin();

?>

<h1 style="text-align: center;">Blog</h1>

<section style="width: 800px; margin: 50px auto 0px;">
    <style>
        article
        {
            margin-bottom: 20px;
            overflow: hidden; /* contains floated thumbnail */
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

    $sql = <<<EOT
SELECT
    LOWER(HEX(`archiveId`)) AS `archiveId`,
    `stub`,
    `titleHTML`,
    `subtitleHTML`
FROM
    `ColbyPages`
WHERE
    `groupId` = UNHEX('{$blogPostsGroupId}') AND
    `published` IS NOT NULL
ORDER BY
    `published` DESC
EOT;

    $result = Colby::query($sql);

    if ($result->num_rows > 0)
    {
        while ($row = $result->fetch_object())
        {
            $dataPath = ColbyArchive::dataPathForArchiveId($row->archiveId);

            $url = COLBY_SITE_URL . "/{$row->stub}/";
            $absoluteThumbnailFilename = COLBY_DATA_DIRECTORY . "/{$dataPath}/thumbnail.jpg";
            $absoluteThumbnailURL = COLBY_DATA_URL . "/{$dataPath}/thumbnail.jpg";

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

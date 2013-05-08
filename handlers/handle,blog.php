<?php

$blogPostsGroupId = '37151457af40ee706cc23de4a11e7ebacafd0c10';

$page = new ColbyOutputManager();

$page->titleHTML = 'Blog';
$page->descriptionHTML = 'Index of blog posts.';

$page->begin();

?>

<main>
    <header>
        <h1>Blog</h1>
    </header>

    <section style="blog-post-summary-list">

        <?php

        $sql = <<<EOT
SELECT
    `stub`,
    `titleHTML`,
    `subtitleHTML`,
    `thumbnailURL`,
    `published`
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
                $postURL = COLBY_SITE_URL . "/{$row->stub}/";

                $publishedDataTimestampAttribute =  $row->published * 1000;
                $publishedDateTimeAttribute = ColbyConvert::timestampToRFC3339($row->published);
                $publishedTextContent = ColbyConvert::timestampToOldBrowserReadableTime($row->published);

                ?>

                <article class="blog-post-summary">
                    <figure>

                        <?php

                        if ($row->thumbnailURL)
                        {
                            ?>

                            <a href="<?php echo $postURL; ?>">
                                <img src="<?php echo $row->thumbnailURL; ?>" alt="">
                            </a>

                            <?php
                        }

                        ?>

                    </figure>

                    <section>

                        <h1><a href="<?php echo $postURL; ?>"><?php echo $row->titleHTML; ?></a></h1>
                        <h2><?php echo $row->subtitleHTML; ?></h2>

                        <time class="value time"
                              datetime="<?php echo $publishedDateTimeAttribute; ?>"
                              data-timestamp="<?php echo $publishedDataTimestampAttribute; ?>">
                            <?php echo $publishedTextContent; ?>
                        </time>

                    </section>
                </article>

                <?php
            }
        }

        $result->free();

        ?>

    </section>
</main>

<?php

$page->end();

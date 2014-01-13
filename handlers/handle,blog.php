<?php

$blogPostsGroupId = '37151457af40ee706cc23de4a11e7ebacafd0c10';
$countOfPostsPerPage = 10;

$page = new ColbyOutputManager();

$page->titleHTML = 'Blog';
$page->descriptionHTML = 'Index of blog posts.';

$page->begin();

$currentPageIndex = isset($_GET['page']) ? max(intval($_GET['page']) - 1, 0) : 0;
$firstPostOffset =  $currentPageIndex * $countOfPostsPerPage;

$sql = <<<EOT
SELECT SQL_CALC_FOUND_ROWS
    `URI`,
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
LIMIT
    {$countOfPostsPerPage}
OFFSET
    {$firstPostOffset}
EOT;

$postsResult = Colby::query($sql);

$countResult = Colby::query('SELECT FOUND_ROWS() AS `countOfAllPosts`');

$countOfAllPosts = $countResult->fetch_object()->countOfAllPosts;
$countOfAllPages = ceil($countOfAllPosts / $countOfPostsPerPage);

$countResult->free();

?>

<main>
    <header>
        <h1>Blog</h1>
    </header>

    <section style="blog-post-summary-list">

        <?php

        if ($postsResult->num_rows > 0)
        {
            while ($row = $postsResult->fetch_object())
            {
                $postURL = COLBY_SITE_URL . "/{$row->URI}/";

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

        $postsResult->free();

        ?>

    </section>

    <section class="page-navigation"><!--

        <?php

            for ($i = 0; $i < $countOfAllPages; $i++)
            {
                $pageNumber = $i + 1;

                if ($i === $currentPageIndex)
                {
                    ?>

                    --><span class="page-number">
                        <?php echo $pageNumber; ?>
                    </span><!--

                    <?php
                }
                else
                {
                    $pageVariable = '';

                    if ($i)
                    {
                        $pageVariable = "?page={$pageNumber}";
                    }

                    $url = COLBY_SITE_URL . "/blog/{$pageVariable}";

                    ?>

                    --><a class="page-number"
                       href="<?php echo $url; ?>"><?php echo $pageNumber; ?></a><!--

                    <?php
                }
            }
        ?>

    --></section>

</main>

<?php

$page->end();

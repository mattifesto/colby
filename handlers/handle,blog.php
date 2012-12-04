<?php

// TODO: better title and description?

$page = ColbyOutputManager::beginPage('Blog', 'Blog');

?>

<h1>Blog</h1>

<section id="blog-posts">
    <style>
        article > h1
        {
            font-size: 1.5em;
        }

        article > h2
        {
            font-size: 1.0em;
        }
    </style>

    <?php

    $sql = <<<EOT
SELECT
    `stub`,
    `titleHTML`,
    `subtitleHTML`
FROM
    `ColbyPages`
WHERE
    `published` IS NOT NULL
ORDER BY
    `published`
EOT;

    $result = Colby::query($sql);

    if ($result->num_rows > 0)
    {
        while ($row = $result->fetch_object())
        {
            $url = COLBY_SITE_URL . "/{$row->stub}/";

            ?>

            <article>
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

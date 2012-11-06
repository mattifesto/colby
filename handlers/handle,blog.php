<?php

// TODO: better title and description?

$args = new stdClass();
$args->title = 'Blog';
$args->description = 'Blog';

ColbyPage::begin($args);

?>

<h1>Blog</h1>

<section id="blog-posts">
    <style>
        article > h1
        {
            font-size: 1.5em;
        }
    </style>

    <?php

    $sql = <<<EOT
SELECT
    LOWER(HEX(`id`)) AS `id`,
    `stub`
FROM
    `ColbyBlogPosts`
ORDER BY
    `published`
EOT;

    $result = Colby::query($sql);

    if ($result->num_rows > 0)
    {
        while ($row = $result->fetch_object())
        {
            $archive = ColbyArchive::open($row->id);
            $data = $archive->rootObject();

            $url = COLBY_SITE_URL . "/blog/{$row->stub}/";

            ?>

            <article>
                <h1><a href="<?php echo $url; ?>"><?php echo $data->titleHTML; ?></a></h1>
            </article>

            <?php
        }
    }

    ?>

</section>

<?php

ColbyPage::end();

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

while ($row = $result->fetch_object())
{
    ?>

    <p><a href="<?php echo COLBY_SITE_URL . "/blog/{$row->stub}/"; ?>"><?php echo $row->stub; ?></a>

    <?php
}

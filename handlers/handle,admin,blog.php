<?php

ColbyPage::requireVerifiedUser();

$args = new stdClass();
$args->title = 'Blog Administration';
$args->description = 'Create, edit, and delete blog posts.';

ColbyPage::beginAdmin($args);

?>

<h1>Blog Administration</h1>

<?php

$sql = <<<EOT
SELECT
    LOWER(HEX(`id`)) AS `id`
FROM
    `ColbyBlogPosts`
ORDER BY
    `published`
EOT;

$result = Colby::query($sql);

?>

<h2>Blog Posts</h2>

<table><tbody>

    <?php

    while ($row = $result->fetch_object())
    {
        echo "<tr><td><a href="">{$row->id}</a></td></tr>\n";
    }

    $result->free();

    ?>

</tbody></table>

<h2>Create a New Blog Post</h2>

<?php

$editorDataFiles = glob(COLBY_SITE_DIRECTORY . '/colby/handlers/handle,admin,blog,*.data');
$editorDataFiles = $editorDataFiles + glob(COLBY_SITE_DIRECTORY . '/handlers/handle,admin,blog,*.data');

foreach ($editorDataFiles as $editorDataFile)
{
    $editorData = unserialize(file_get_contents($editorDataFile));

    preg_match('/blog,([^,]*).data$/', $editorDataFile, $matches);

    echo "<p>{$editorData->name} <a href=\"/admin/blog/{$matches[1]}/edit/\">new</a>\n";
}

ColbyPage::end();

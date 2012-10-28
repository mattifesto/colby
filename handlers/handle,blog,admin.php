<?php

$args = new stdClass();
$args->title = 'Blog Administration';
$args->description = 'Create, edit, and delete blog posts.';

ColbyPage::beginAdmin($args);

?>

<h1>Blog Administration</h1>

<?php

$sql = <<<EOT
SELECT
    `id`
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
        echo "<tr><td>{$row->id}</td></tr>\n";
    }

    $result->free();

    ?>

</tbody></table>

<h2>Create a New Blog Post</h2>

<?php

$editorDataFiles = glob(COLBY_SITE_DIRECTORY . '/colby/handlers/handle,blog,editor,*.data');
$editorDataFiles = $editorDataFiles + glob(COLBY_SITE_DIRECTORY . '/handlers/handle,blog,editor,*.data');

foreach ($editorDataFiles as $editorDataFile)
{
    $editorData = unserialize(file_get_contents($editorDataFile));

    preg_match('/blog,editor,(.*).data$/', $editorDataFile, $matches);

    echo "<p>{$editorData->name} <a href=\"/blog/editor/{$matches[1]}/\">new</a>\n";
}

ColbyPage::end();

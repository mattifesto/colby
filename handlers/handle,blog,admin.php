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

$editorDataFiles = glob(COLBY_SITE_DIRECTORY . '/colby/editor-templates/*/data.php');
$editorDataFiles = $editorDataFiles + glob(COLBY_SITE_DIRECTORY . '/editor-templates/*/data.php');

foreach ($editorDataFiles as $editorDataFile)
{
    $editorData = unserialize(file_get_contents($editorDataFile));
    
    echo "<p>{$editorData->internalType}\n";
}

?>

<?php

ColbyPage::end();

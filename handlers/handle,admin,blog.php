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
    LOWER(HEX(`type`)) AS `type`,
    LOWER(HEX(`id`)) AS `id`
FROM
    `ColbyBlogPosts`
ORDER BY
    `published`
EOT;

$result = Colby::query($sql);

?>

<h2>Blog Posts</h2>

<table style="width: 600px;"><thead>
    <tr>
        <th style="width: 30px;"></th>
        <th style="width: 400px;">Title</th>
        <th style="width: 100px;">Created</th>
        <th style="width: 100px;">Modified</th>
    </tr>
</thead><tbody>

    <?php

    while ($row = $result->fetch_object())
    {
        $archive = ColbyArchive::open($row->id);
        $attributes = $archive->attributes();
        $data = $archive->rootObject();

        $editURL = COLBY_SITE_URL . "/admin/blog/{$row->type}/edit/?archive-id={$row->id}";

        ?>

        <tr>
            <td><a href="<?php echo $editURL; ?>">edit</a></td>
            <td><?php echo $data->titleHTML; ?></td>
            <td><?php echo gmdate('Y/m/d', $attributes->created); ?></td>
            <td><?php echo gmdate('Y/m/d', $attributes->modified); ?></td>
        </tr>

        <?php
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

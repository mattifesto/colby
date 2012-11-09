<?php

ColbyPage::requireVerifiedUser();

$args = new stdClass();
$args->title = 'Blog';
$args->description = 'Create, edit, and delete blog posts.';

ColbyPage::beginAdmin($args);

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

<?php

$editorDataFiles = glob(COLBY_SITE_DIRECTORY . '/colby/handlers/handle,admin,blog,*.data');
$editorDataFiles = $editorDataFiles + glob(COLBY_SITE_DIRECTORY . '/handlers/handle,admin,blog,*.data');

foreach ($editorDataFiles as $editorDataFile)
{
    $editorData = unserialize(file_get_contents($editorDataFile));

    preg_match('/blog,([^,]*).data$/', $editorDataFile, $matches);

    $editorURL = "/admin/blog/{$matches[1]}/edit/";

    // TODO: escape this data for HTML

    ?>

    <p style="font-size: 1.5em;"><a href="<?php echo $editorURL ?>">Create a <?php echo $editorData->name; ?></a>
    <p><?php echo $editorData->description; ?>

    <?php
}

ColbyPage::end();

<?php

$page = ColbyOutputManager::beginVerifiedUserPage('Blog', 'Create, edit, and delete blog posts.', 'admin');

$blogPostGroupId = '37151457af40ee706cc23de4a11e7ebacafd0c10';

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

        $editURL = COLBY_SITE_URL . "/admin/model/{$row->type}/edit/?archive-id={$row->id}&group-id={$blogPostGroupId}&group-stub=blog";

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

$modelDataFiles = glob(COLBY_SITE_DIRECTORY . '/colby/handlers/handle,admin,model,*.data');
$modelDataFiles = array_merge($modelDataFiles,
                              glob(COLBY_SITE_DIRECTORY . '/handlers/handle,admin,model,*.data'));

foreach ($modelDataFiles as $modelDataFile)
{
    // Get the model id

    preg_match('/model,([^,]*).data$/', $modelDataFile, $matches);

    // If there is a blog view for this model, offer the model as an option

    $viewHandlerFilename = "handle,admin,blog,{$matches[1]},view.php";

    if (Colby::findHandler($viewHandlerFilename))
    {
        $modelData = unserialize(file_get_contents($modelDataFile));

        $editURL = "/admin/model/{$matches[1]}/edit/";

        ?>

        <p style="font-size: 1.5em;"><a href="<?php echo $editURL; ?>">Create a <?php echo $modelData->nameHTML; ?></a>
        <p><?php echo $modelData->descriptionHTML; ?>

        <?php
    }
}

$page->end();

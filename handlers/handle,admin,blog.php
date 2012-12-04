<?php

$page = ColbyOutputManager::beginVerifiedUserPage('Blog', 'Create, edit, and delete blog posts.', 'admin');

$blogPostGroupId = '37151457af40ee706cc23de4a11e7ebacafd0c10';
$blogPostGroupStub = 'blog';

$sql = <<<EOT
SELECT
    LOWER(HEX(`archiveId`)) AS `archiveId`,
    LOWER(HEX(`modelId`)) AS `modelId`,
    `titleHTML`,
    `published`
FROM
    `ColbyPages`
WHERE
    `groupId` = UNHEX('{$blogPostGroupId}')
ORDER BY
    `published`
EOT;

$result = Colby::query($sql);

?>

<table style="width: 600px;"><thead>
    <tr>
        <th style="width: 30px;"></th>
        <th style="width: 400px;">Title</th>
        <th style="width: 100px;">Published</th>
    </tr>
</thead><tbody>

    <?php

    while ($row = $result->fetch_object())
    {
        $editURL = COLBY_SITE_URL . "/admin/model/{$row->modelId}/edit/?archive-id={$row->archiveId}&group-id={$blogPostGroupId}&group-stub={$blogPostGroupStub}";

        ?>

        <tr>
            <td><a href="<?php echo $editURL; ?>">edit</a></td>
            <td><?php echo $row->titleHTML; ?></td>
            <td><?php echo $row->published; ?></td>
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

        $editURL = "/admin/model/{$matches[1]}/edit/?group-id={$blogPostGroupId}&group-stub={$blogPostGroupStub}";

        ?>

        <p style="font-size: 1.5em;"><a href="<?php echo $editURL; ?>">Create a <?php echo $modelData->nameHTML; ?></a>
        <p><?php echo $modelData->descriptionHTML; ?>

        <?php
    }
}

$page->end();

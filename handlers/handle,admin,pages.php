<?php

$page = new ColbyOutputManager('admin-html-page');

$page->titleHTML = 'Pages';
$page->descriptionHTML = 'Create, edit, and delete pages.';

$page->begin();

if (!ColbyUser::current()->isOneOfThe('Administrators'))
{
    include Colby::findSnippet('authenticate.php');

    goto done;
}

$pagesDocumentGroupId = 'a3f5d7ead80d4e6cb644ec158a13f3a89a9a0622';

$pagesDocumentGroupData = unserialize(file_get_contents(
    Colby::findFileForDocumentGroup('document-group.data', $pagesDocumentGroupId)
    ));

$sql = <<<EOT
SELECT
    LOWER(HEX(`archiveId`)) AS `archiveId`,
    LOWER(HEX(`modelId`)) AS `modelId`,
    `titleHTML`,
    `published`
FROM
    `ColbyPages`
WHERE
    `groupId` = UNHEX('{$pagesDocumentGroupId}')
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
        $editURL = COLBY_SITE_URL . "/admin/model/{$row->modelId}/edit/?archive-id={$row->archiveId}";

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

$viewDataFiles = glob(COLBY_SITE_DIRECTORY . '/colby/handlers/handle,admin,view,*.data');
$viewDataFiles = array_merge($viewDataFiles,
                              glob(COLBY_SITE_DIRECTORY . '/handlers/handle,admin,view,*.data'));

foreach ($viewDataFiles as $viewDataFile)
{
    $viewData = unserialize(file_get_contents($viewDataFile));

    if ($viewData->groupId != $pagesDocumentGroupId)
    {
        continue;
    }

    // Get the view id

    preg_match('/([0-9a-f]{40})/', $viewDataFile, $matches);

    $viewId = $matches[1];

    $editURL = "/admin/model/{$viewData->modelId}/edit/?&view-id={$viewId}";

    ?>

    <p style="font-size: 1.5em;"><a href="<?php echo $editURL; ?>">Create a <?php echo $viewData->nameHTML; ?></a>
    <p><?php echo $viewData->descriptionHTML; ?>

    <?php
}

done:

$page->end();

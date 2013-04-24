<?php

$page = new ColbyOutputManager('admin-html-page');

$page->titleHTML = 'Blog Administration';
$page->descriptionHTML = 'Create, edit, and delete blog posts.';

$page->begin();

if (!ColbyUser::current()->isOneOfThe('Administrators'))
{
    include Colby::findSnippet('authenticate.php');

    goto done;
}


$blogPostDocumentGroupId = '37151457af40ee706cc23de4a11e7ebacafd0c10';

$sql = <<<EOT
SELECT
    LOWER(HEX(`archiveId`)) AS `archiveId`,
    LOWER(HEX(`modelId`)) AS `documentTypeId`,
    `titleHTML`,
    `published`
FROM
    `ColbyPages`
WHERE
    `groupId` = UNHEX('{$blogPostDocumentGroupId}')
ORDER BY
    `published` DESC
EOT;

$result = Colby::query($sql);

?>

<main>
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
            $editURL = COLBY_SITE_URL . "/admin/document/edit/" .
                "?document-group-id={$blogPostDocumentGroupId}" .
                "&document-type-id={$row->documentTypeId}" .
                "&archive-id={$row->archiveId}";

            ?>

            <tr>
                <td><a href="<?php echo $editURL; ?>">edit</a></td>
                <td><?php echo $row->titleHTML; ?></td>
                <td><span class="time"
                          data-timestamp="<?php echo $row->published ? $row->published * 1000 : ''; ?>">
                    </span></td>
            </tr>

            <?php
        }

        $result->free();

        ?>

    </tbody></table>

    <?php

    $documentTypes = Colby::findDocumentTypes($blogPostDocumentGroupId);

    foreach ($documentTypes as $documentType)
    {
        $createNewBlogPostURL = COLBY_SITE_URL .
            '/admin/document/edit/' .
            "?document-group-id={$blogPostDocumentGroupId}" .
            "&document-type-id={$documentType->id}";

        ?>
        <div><a href="<?php echo $createNewBlogPostURL; ?>">
            <?php echo $documentType->nameHTML; ?>
        </a></div>
        <?php
    }

    ?>

</main>

<?php

done:

$page->end();

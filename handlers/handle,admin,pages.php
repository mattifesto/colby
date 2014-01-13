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

$sql = <<<EOT
SELECT
    LOWER(HEX(`archiveID`)) AS `archiveId`,
    LOWER(HEX(`typeID`)) AS `documentTypeId`,
    `titleHTML`,
    `published`
FROM
    `ColbyPages`
WHERE
    `groupID` = UNHEX('{$pagesDocumentGroupId}')
ORDER BY
    ISNULL(`published`) DESC,
    `published` DESC
EOT;

$result = Colby::query($sql);

?>

<main>
    <h1>Pages</h1>

    <table class="document-list">
        <thead><tr>
            <th></th>
            <th>Title</th>
            <th>Published</th>
        </tr></thead>
        <tbody>

        <?php

        while ($row = $result->fetch_object())
        {
            $editURL = COLBY_SITE_URL . "/admin/document/edit/" .
                "?document-group-id={$pagesDocumentGroupId}" .
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

    <h2>Create a new document</h2>

    <?php

    $documentTypes = Colby::findDocumentTypes($pagesDocumentGroupId);

    foreach ($documentTypes as $documentType)
    {
        $createNewPageURL = COLBY_SITE_URL .
            '/admin/document/edit/' .
            "?document-group-id={$pagesDocumentGroupId}" .
            "&document-type-id={$documentType->id}";

        ?>
        <div><a href="<?php echo $createNewPageURL; ?>">
            <?php echo $documentType->nameHTML; ?>
        </a></div>
        <?php
    }

    ?>

</main>

<?php

done:

$page->end();

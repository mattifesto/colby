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
    LOWER(HEX(`archiveID`)) AS `archiveId`,
    LOWER(HEX(`typeID`)) AS `documentTypeId`,
    `titleHTML`,
    `published`
FROM
    `ColbyPages`
WHERE
    `groupID` = UNHEX('{$blogPostDocumentGroupId}')
ORDER BY
    ISNULL(`published`) DESC,
    `published` DESC
EOT;

$result = Colby::query($sql);

?>

<main>
    <h1>Blog Posts</h1>

    <table class="document-list"><thead>
        <tr>
            <th></th>
            <th>Title</th>
            <th>Published</th>
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

    <h2>Create a new document</h2>

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

<?php

define('CBOldStylePagesGroupId', 'a3f5d7ead80d4e6cb644ec158a13f3a89a9a0622');

include_once CBSystemDirectory . '/classes/CBHTMLOutput.php';


CBHTMLOutput::setTitleHTML('Page Administration');
CBHTMLOutput::setDescriptionHTML('Create, edit, and delete pages.');
CBHTMLOutput::addJavaScriptURL(CBSystemURL . '/handlers/handle,admin,pages.js');
CBHTMLOutput::begin();


if (!ColbyUser::current()->isOneOfThe('Administrators'))
{
    include Colby::findSnippet('authenticate.php');

    goto done;
}


include CBSystemDirectory . '/sections/admin-page-header.php';

$selectedMenuItemID     = 'pages';
$selectedSubmenuItemID  = 'old-style';

include CBSystemDirectory . '/sections/admin-page-menu.php';

?>

<main>

    <table class="document-list">
        <thead><tr>
            <th></th>
            <th>Title</th>
            <th>Published</th>
        </tr></thead>
        <tbody>

        <?php

        $groupID    = CBOldStylePagesGroupId;
        $result     = CBOldStylePagesAdmin::queryForPages();

        while ($row = $result->fetch_object())
        {
            $editURL = COLBY_SITE_URL . "/admin/document/edit/" .
                "?document-group-id={$groupID}" .
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

    $documentTypes = Colby::findDocumentTypes($groupID);

    foreach ($documentTypes as $documentType)
    {
        $createNewPageURL = COLBY_SITE_URL .
            '/admin/document/edit/' .
            "?document-group-id={$groupID}" .
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

include CBSystemDirectory . '/sections/admin-page-footer.php';

done:

CBHTMLOutput::render();


/**
 *
 */
class CBOldStylePagesAdmin
{
    /**
     * @return mysqli_result
     */
    public static function queryForPages()
    {
        $groupID = CBOldStylePagesGroupId;

        $sql = <<<EOT

        SELECT
            LOWER(HEX(`archiveID`)) AS `archiveId`,
            LOWER(HEX(`typeID`)) AS `documentTypeId`,
            `titleHTML`,
            `published`
        FROM
            `ColbyPages`
        WHERE
            `groupID` = UNHEX('{$groupID}')
        ORDER BY
            ISNULL(`published`) DESC,
            `published` DESC

EOT;

        return Colby::query($sql);
    }
}


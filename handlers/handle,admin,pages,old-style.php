<?php

if (!ColbyUser::current()->isOneOfThe('Administrators'))
{
    return include CBSystemDirectory . '/handlers/handle-authorization-failed.php';
}

CBHTMLOutput::setTitleHTML('Page Administration');
CBHTMLOutput::setDescriptionHTML('Create, edit, and delete pages.');
CBHTMLOutput::addJavaScriptURL(CBSystemURL . '/handlers/handle,admin,pages.js');
CBHTMLOutput::begin();

include CBSystemDirectory . '/sections/admin-page-settings.php';

CBHTMLOutput::addCSSURL(CBSystemURL . '/handlers/handle,admin,pages,old-style.css');

$spec                           = new stdClass();
$spec->selectedMenuItemName     = 'pages';
$spec->selectedSubmenuItemName  = 'old-style';

CBAdminPageMenuView::renderModelAsHTML(CBAdminPageMenuView::specToModel($spec));

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

        $groupID    = COLBY_PAGES_DOCUMENT_GROUP_ID;
        $result     = CBOldStylePagesAdmin::queryForPages(COLBY_PAGE_DOCUMENT_TYPE_ID, $groupID);

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

    <table class="document-list" style="margin-top: 100px;">
        <thead><tr>
            <th></th>
            <th>Title</th>
            <th>Published</th>
        </tr></thead>
        <tbody>

        <?php

        $groupID    = COLBY_BLOG_POSTS_DOCUMENT_GROUP_ID;
        $result     = CBOldStylePagesAdmin::queryForPages(COLBY_BLOG_POST_DOCUMENT_TYPE_ID, $groupID);

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

    <h2>Create a new blog post</h2>

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

CBAdminPageFooterView::renderModelAsHTML();

CBHTMLOutput::render();


/**
 *
 */
class CBOldStylePagesAdmin
{
    /**
     * @return mysqli_result
     */
    public static function queryForPages($typeID, $groupID)
    {
        $typeIDForSQL   = ColbyConvert::textToSQL($typeID);
        $groupIDForSQL  = ColbyConvert::textToSQL($groupID);

        $sql = <<<EOT

        SELECT
            LOWER(HEX(`archiveID`)) AS `archiveId`,
            LOWER(HEX(`typeID`)) AS `documentTypeId`,
            `titleHTML`,
            `published`
        FROM
            `ColbyPages`
        WHERE
            `typeID`    = UNHEX('{$typeIDForSQL}')  AND
            `groupID`   = UNHEX('{$groupIDForSQL}')
        ORDER BY
            ISNULL(`published`) DESC,
            `published` DESC

EOT;

        return Colby::query($sql);
    }
}


<?php

if (!ColbyUser::current()->isOneOfThe('Administrators'))
{
    return include CBSystemDirectory . '/handlers/handle-authorization-failed.php';
}


CBHTMLOutput::begin();
CBHTMLOutput::setTitleHTML('Unpublished Pages');
CBHTMLOutput::setDescriptionHTML('Pages that haven\'t been published.');

include CBSystemDirectory . '/sections/admin-page-settings.php';

CBHTMLOutput::addCSSURL(CBSystemURL . '/handlers/handle,admin,pages.css');
CBHTMLOutput::addJavaScriptURL(CBSystemURL . '/handlers/handle,admin,pages.js');

$selectedMenuItemID     = 'pages';
$selectedSubmenuItemID  = 'unpublished';

include CBSystemDirectory . '/sections/admin-page-menu.php';

?>

<main>

    <table class="list-of-pages">
        <thead><tr>
            <th class="actions-cell" style="width: 150px;"></th>
            <th class="title-cell">Title</th>
            <th class="publication-date-cell">Publication Date</th>
        </tr></thead>
        <tbody class="standard-admin-row-colors">

        <?php

        $result = CBPagesAdmin::queryForPages();

        while ($row = $result->fetch_object())
        {
            $elementID = "id-{$row->dataStoreID}";

            $editURL        = COLBY_SITE_URL . "/admin/pages/edit/?data-store-id={$row->dataStoreID}";
            $editAction     = "location.href = '{$editURL}'";
            $deleteAction   = "CBPagesAdmin.movePageWithDataStoreIDToTrash('{$row->dataStoreID}');";

            ?>

            <tr id="<?php echo $elementID; ?>">
                <td class="actions-cell">
                    <button onclick="<?php echo $editAction; ?>">Edit</button><!--
                    --><button onclick="<?php echo $deleteAction; ?>">Move to Trash</button>
                </td>
                <td class="title-cell"><?php echo $row->titleHTML; ?></td>
                <td class="publication-date-cell"><span class="time"
                          data-timestamp="<?php echo $row->published ? $row->published * 1000 : ''; ?>">
                    </span></td>
            </tr>

            <?php
        }

        ?>

    </tbody></table>

</main>

<?php

include CBSystemDirectory . '/sections/admin-page-footer-2.php';

CBHTMLOutput::render();


/**
 *
 */
class CBPagesAdmin
{
    /**
     * @return mysqli_result
     */
    public static function queryForPages()
    {
        $sql = <<<EOT

        SELECT
            LOWER(HEX(`archiveID`)) AS `dataStoreID`,
            `titleHTML`,
            `published`
        FROM
            `ColbyPages`
        WHERE
            `className` = 'CBViewPage' AND
            `published` IS NULL
        ORDER BY
            `ID` DESC

EOT;

        return Colby::query($sql);
    }
}

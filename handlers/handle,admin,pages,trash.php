<?php

if (!ColbyUser::current()->isOneOfThe('Administrators')) {
    return include CBSystemDirectory . '/handlers/handle-authorization-failed.php';
}

include_once CBSystemDirectory . '/classes/CBHTMLOutput.php';

CBHTMLOutput::begin();
CBHTMLOutput::$classNameForSettings = 'CBPageSettingsForAdminPages';
CBHTMLOutput::setTitleHTML('Pages Trash');
CBHTMLOutput::setDescriptionHTML('Pages that have been put in the trash.');
CBHTMLOutput::addCSSURL(CBSystemURL . '/handlers/handle,admin,pages.css');
CBHTMLOutput::addJavaScriptURL(CBSystemURL . '/handlers/handle,admin,pages,trash.js');

$selectedMenuItemID     = 'pages';
$selectedSubmenuItemID  = 'trash';

include CBSystemDirectory . '/sections/admin-page-menu.php';

?>

<main class="CBSystemFont">

    <table class="list-of-pages">
        <thead><tr>
            <th class="actions-cell" style="width: 200px;"></th>
            <th class="title-cell">Title</th>
            <th class="publication-date-cell">Publication Date</th>
        </tr></thead>
        <tbody class="standard-admin-row-colors">

        <?php

        $result = CBPagesAdmin::queryForPagesInTheTrash();

        while ($row = $result->fetch_object())
        {
            $elementID = "id-{$row->dataStoreID}";

            $recoverAction  = "CBPagesInTheTrash.recoverPageWithDataStoreID('{$row->dataStoreID}');";
            $deleteAction   = "CBPagesInTheTrash.deletePageWithDataStoreID('{$row->dataStoreID}');";

            ?>

            <tr id="<?php echo $elementID; ?>">
                <td class="actions-cell">
                    <button onclick="<?php echo $recoverAction; ?>">Recover</button><!--
                    --><button onclick="<?php echo $deleteAction; ?>">Delete Permanently</button>
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
    public static function queryForPagesInTheTrash()
    {
        $sql = <<<EOT

        SELECT
            LOWER(HEX(`dataStoreID`)) AS `dataStoreID`,
            `titleHTML`,
            `published`
        FROM
            `CBPagesInTheTrash`
        ORDER BY
            `ID` DESC

EOT;

        return Colby::query($sql);
    }
}

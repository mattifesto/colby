<?php

include_once CBSystemDirectory . '/classes/CBHTMLOutput.php';


CBHTMLOutput::setTitleHTML('Page Administration');
CBHTMLOutput::setDescriptionHTML('Create, edit, and delete pages.');
CBHTMLOutput::addJavaScriptURL(CBSystemURL . '/handlers/handle,admin,pages.js');
CBHTMLOutput::begin();


if (!ColbyUser::current()->isOneOfThe('Administrators'))
{
    include Colby::findSnippet('authenticate.php');

    // TODO: this should display a whole new page instead of a snippet and return instead of goto

    goto done;
}


include CBSystemDirectory . '/sections/admin-page-header.php';

$selectedMenuItemID     = 'pages';
$selectedSubmenuItemID  = 'unpublished';

include CBSystemDirectory . '/sections/admin-page-menu.php';

?>

<main>

    <table class="standard-cell-ellipses">
        <style>

            th.actions,
            tr.actions
            {
                width: 100px;
            }

        </style>
        <thead><tr>
            <th class="actions"></th>
            <th>Title</th>
            <th>Published</th>
        </tr></thead>
        <tbody>

        <?php

        $result = CBPagesAdmin::queryForPages();

        while ($row = $result->fetch_object())
        {
            $elementID = "s{$row->dataStoreID}";

            $editURL = COLBY_SITE_URL . "/admin/pages/edit/" .
                "?data-store-id={$row->dataStoreID}";

            $deleteAction = "CBPagesAdmin.deletePageByDataStoreID('{$row->dataStoreID}');";

            ?>

            <tr id="<?php echo $elementID; ?>">
                <td class="actions">
                    <a href="<?php echo $editURL; ?>">edit</a>
                    <a onclick="<?php echo $deleteAction; ?>" style="cursor: pointer;">delete</a>
                </td>
                <td class="title"><?php echo $row->titleHTML; ?></td>
                <td class="publication-date"><span class="time"
                          data-timestamp="<?php echo $row->published ? $row->published * 1000 : ''; ?>">
                    </span></td>
            </tr>

            <?php
        }

        ?>

    </tbody></table>

</main>

<?php

include CBSystemDirectory . '/sections/admin-page-footer.php';

done:

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
        $CBPageTypeID = CBPageTypeID;

        $sql = <<<EOT

        SELECT
            LOWER(HEX(`archiveID`)) AS `dataStoreID`,
            LOWER(HEX(`typeID`)) AS `typeID`,
            `titleHTML`,
            `published`
        FROM
            `ColbyPages`
        WHERE
            `typeID` = UNHEX('{$CBPageTypeID}') AND
            `published` IS NULL
        ORDER BY
            `ID` DESC

EOT;

        return Colby::query($sql);
    }
}


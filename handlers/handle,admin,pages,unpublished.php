<?php

include_once CBSystemDirectory . '/classes/CBHTMLOutput.php';


if (!ColbyUser::current()->isOneOfThe('Administrators'))
{
    return include CBSystemDirectory . '/handlers/handle-authorization-failed.php';
}


CBHTMLOutput::begin();
CBHTMLOutput::setTitleHTML('Page Administration');
CBHTMLOutput::setDescriptionHTML('Create, edit, and delete pages.');

include CBSystemDirectory . '/sections/equalize.php';

CBHTMLOutput::addCSSURL(CBSystemURL . '/css/admin.css');
CBHTMLOutput::addCSSURL(CBSystemURL . '/handlers/handle,admin,pages.css');
CBHTMLOutput::addCSSURL('https://fonts.googleapis.com/css?family=Source+Sans+Pro:400');
CBHTMLOutput::addCSSURL('https://fonts.googleapis.com/css?family=Source+Sans+Pro:700');
CBHTMLOutput::addJavaScriptURL(CBSystemURL . '/javascript/Colby.js');
CBHTMLOutput::addJavaScriptURL(CBSystemURL . '/handlers/handle,admin,pages.js');


$selectedMenuItemID     = 'pages';
$selectedSubmenuItemID  = 'unpublished';

include CBSystemDirectory . '/sections/admin-page-menu.php';

?>

<main>

    <table class="list-of-pages standard-admin-spacing standard-cell-ellipses">
        <thead><tr>
            <th class="actions-cell"></th>
            <th class="title-cell">Title</th>
            <th class="publication-date-cell">Publication Date</th>
        </tr></thead>
        <tbody class="standard-admin-row-colors">

        <?php

        $result = CBPagesAdmin::queryForPages();

        while ($row = $result->fetch_object())
        {
            $elementID = "id-{$row->dataStoreID}";

            $editURL = COLBY_SITE_URL . "/admin/pages/edit/" .
                "?data-store-id={$row->dataStoreID}";

            $deleteAction = "CBPagesAdmin.deletePageWithDataStoreID('{$row->dataStoreID}');";

            ?>

            <tr id="<?php echo $elementID; ?>">
                <td class="actions-cell">
                    <a href="<?php echo $editURL; ?>">edit</a>
                    <a onclick="<?php echo $deleteAction; ?>" style="cursor: pointer;">delete</a>
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
        $CBPageTypeID = CBPageTypeID;

        $sql = <<<EOT

        SELECT
            LOWER(HEX(`archiveID`)) AS `dataStoreID`,
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


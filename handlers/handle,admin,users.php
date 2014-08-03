<?php

if (!ColbyUser::current()->isOneOfThe('Developers'))
{
    return include CBSystemDirectory . '/handlers/handle-authorization-failed.php';
}

CBHTMLOutput::begin();
CBHTMLOutput::setTitleHTML('Permissions');
CBHTMLOutput::setDescriptionHTML('Manage user permissions.');

include CBSystemDirectory . '/sections/admin-page-settings.php';

CBHTMLOutput::addJavaScriptURL(CBSystemURL . '/handlers/handle,admin,users.js');

CBHTMLOutput::addCSSURL('https://fonts.googleapis.com/css?family=Source+Sans+Pro:400');
CBHTMLOutput::addCSSURL('https://fonts.googleapis.com/css?family=Source+Sans+Pro:700');

$menu = CBAdminPageMenuView::init();
$menu->setSelectedMenuItemName('general');
$menu->setSelectedSubmenuItemName('permissions');
$menu->renderHTML();

/**
 *
 */

$sql = <<<EOT

    SELECT
        USER() as `user`,
        CURRENT_USER() as `currentUser`

EOT;

$result = Colby::query($sql);

$row = $result->fetch_object();

$result->free();

?>

<main>
    <style scoped>

        main
        {
            font-family: "Source Sans Pro";
        }

        table.permissions-administration
        {
            table-layout:   fixed;
            margin:         0px auto;
        }

        table.permissions-administration td,
        table.permissions-administration th
        {
            padding: 2px 5px;
        }

        table.database-user-information
        {
            width: 800px;
            margin-bottom: 50px;
        }

        table.database-user-information td,
        table.database-user-information th
        {
            width: 50%;
        }

        table.database-user-information th
        {
            text-align: right;
        }

    </style>

    <table class="permissions-administration database-user-information">
        <tbody>
            <tr>
                <th>MySQL USER()</th>
                <td><?php echo $row->user; ?></td>
            </tr>
            <tr>
                <th>MySQL CURRENT_USER()</th>
                <td><?php echo $row->currentUser; ?></td>
            </tr>
        </tbody>
    </table>


    <?php

    /**
     *
     */

    $sql = <<<EOT

        SELECT
            `TABLE_NAME` AS `tableName`
        FROM
            information_schema.TABLES
        WHERE
            `TABLE_SCHEMA` = DATABASE() AND
            `TABLE_NAME` LIKE 'ColbyUsersWhoAre%'

EOT;

    $result = Colby::query($sql);

    $friendlyNames = array();
    $tableNames = array();
    $columnNames = array();
    $joins = array();

    while ($row = $result->fetch_object())
    {
        preg_match('/ColbyUsersWhoAre(.*)/', $row->tableName, $matches);

        $friendlyName = $matches[1];
        $friendlyNames[] = $friendlyName;
        $tableNames[] = $row->tableName;
        $columnNames[] = "`{$row->tableName}`.`added` AS `isIn{$friendlyName}`";
        $joins[] = "LEFT JOIN `{$row->tableName}` ON `ColbyUsers`.`id` = `{$row->tableName}`.`userId`";
    }

    $result->free();

    /**
     *
     */

    $columnNamesClause = implode(",\n", $columnNames);
    $joinsClause = implode("\n", $joins);

    $sql = <<<EOT

        SELECT
            `ColbyUsers`.`id`,
            `ColbyUsers`.`facebookId`,
            `ColbyUsers`.`facebookName`,
            `ColbyUsers`.`hasBeenVerified`,
            {$columnNamesClause}
        FROM
            `ColbyUsers`
        {$joinsClause}
        ORDER BY
            `ColbyUsers`.`facebookLastName`

EOT;

    $result = Colby::query($sql);

    ?>

    <table class="permissions-administration">
        <thead>
            <tr>
                <th>Id</th>
                <th>Name</th>

                <?php

                foreach ($friendlyNames as $friendlyName)
                {
                    echo "<th>{$friendlyName}</th>\n";
                }

                ?>

            </tr>
        </thead>
        <tbody>

            <?php

            while ($row = $result->fetch_object())
            {
                ?>

                <tr>
                    <td><?php echo $row->id; ?></td>
                    <td><?php echo $row->facebookName; ?></td>

                    <?php

                    foreach ($friendlyNames as $friendlyName)
                    {
                        $columnName = "isIn{$friendlyName}";

                        echo '<td style="text-align: center;">';

                        renderCheckbox($row->id, $friendlyName, $row->$columnName);

                        echo '</td>';
                    }

                    ?>

                </tr>

                <?php
            }

            ?>

        </tbody>
    </table>
</main>

<?php

$result->free();

$footer = CBAdminPageFooterView::init();
$footer->renderHTML();

CBHTMLOutput::render();


//
// functions
//

function renderCheckbox($userId, $groupName, $isInGroup)
{
    $checked = '';

    if ($isInGroup)
    {
        $checked = 'checked="checked"';
    }

    ?>

    <input type="checkbox"
           <?php echo $checked, "\n"; ?>
           onclick="ColbyUserManagerViewController.updateUserPermissions(<?php echo $userId ?>, <?php echo "'{$groupName}'"; ?>, this);">

    <?php
}

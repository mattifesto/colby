<?php

$page = new ColbyOutputManager('admin-html-page');

$page->titleHTML = 'Users';
$page->descriptionHTML = 'Manage users.';

$page->begin();

if (!ColbyUser::current()->isOneOfThe('Developers'))
{
    include Colby::findSnippet('authenticate.php');

    goto done;
}

/**
 *
 */

$sql = <<<END
SELECT
    USER() as `user`,
    CURRENT_USER() as `currentUser`
END;

$result = Colby::query($sql);

$row = $result->fetch_object();

$result->free();

?>

<table><thead>
<tr><th>user()</th><th>current_user()</th></tr>
</thead><tbody>
<tr><td><?php echo $row->user; ?></td><td><?php echo $row->currentUser; ?></td></tr>
</tbody></table>

<?php

/**
 *
 */

$sql = <<<END
SELECT
    `TABLE_NAME` AS `tableName`
FROM
    information_schema.TABLES
WHERE
    `TABLE_SCHEMA` = DATABASE() AND
    `TABLE_NAME` LIKE 'ColbyUsersWhoAre%'
END;

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

$sql = <<<END
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
END;

$result = Colby::query($sql);

?>

<table>
    <style scoped>
    td, th
    {
        padding: 2px 5px;
    }
    </style>
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

<script src="<?php echo COLBY_SYSTEM_URL, '/handlers/handle,admin,users.js'; ?>"></script>

<?php

$result->free();

done:

$page->end();


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

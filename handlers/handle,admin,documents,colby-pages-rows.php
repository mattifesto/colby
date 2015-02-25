<?php

if (!ColbyUser::current()->isOneOfThe('Administrators'))
{
    return include CBSystemDirectory . '/handlers/handle-authorization-failed.php';
}

include_once COLBY_SYSTEM_DIRECTORY . '/snippets/shared/documents-administration.php';

CBHTMLOutput::begin();
CBHTMLOutput::setTitleHTML('ColbyPages Table Rows');
CBHTMLOutput::setDescriptionHTML('Pages that are in the ColbyPages table.');

include CBSystemDirectory . '/sections/admin-page-settings.php';

$menu = CBAdminPageMenuView::init();
$menu->setSelectedMenuItemName('develop');
$menu->setSelectedSubmenuItemName('documents');
$menu->renderHTML();

$sql = <<<EOT

    SELECT
        LOWER(HEX(`groupID`)) as `groupId`,
        LOWER(HEX(`typeID`)) as `typeId`,
        LOWER(HEX(`archiveID`)) as `archiveId`
    FROM
        `ColbyPages`
    WHERE
        `className` IS NULL
    ORDER BY
        `groupID`, `typeID`

EOT;

$result = Colby::query($sql);

/**
 * Aggregate data to simplify report generation.
 */

$documentGroupNamesForHTML = array();
$docuemntTypeNamesForHTML = array();

while ($row = $result->fetch_object())
{
    if (!isset($sections) ||
        $row->groupId != $section->groupId ||
        $row->typeId != $section->typeId)
    {
        if (!isset($sections))
        {
            $sections = array();
        }

        $section = new stdClass();

        /**
         * Get the group name.
         */

        if (!$row->groupId)
        {
            $section->groupNameHTML = 'No Group';
        }
        else if (!isset($documentGroupNamesForHTML[$row->groupId]))
        {
            $filename = Colby::findFileForDocumentGroup('document-group.data', $row->groupId);

            if ($filename)
            {
                $groupData = unserialize(file_get_contents($filename));

                $section->groupNameHTML = $groupData->nameHTML;
            }
            else
            {
                $section->groupNameHTML = 'Unknown';
            }

            $documentGroupNamesForHTML[$row->groupId] = $section->groupNameHTML;
        }
        else
        {
            $section->groupNameHTML = $documentGroupNamesForHTML[$row->groupId];
        }

        /**
         * Get the type name.
         */

        if (!$row->typeId)
        {
            $section->typeNameHTML = 'No Type';
        }
        else if (!isset($documentTypeNamesForHTML[$row->typeId]))
        {
            $filename = Colby::findFileForDocumentType('document-type.data', $row->groupId, $row->typeId);

            if ($filename)
            {
                $groupData = unserialize(file_get_contents($filename));

                $section->typeNameHTML = $groupData->nameHTML;
            }
            else
            {
                $section->typeNameHTML = 'Unknown';
            }

            $documentTypeNamesForHTML[$row->typeId] = $section->typeNameHTML;
        }
        else
        {
            $section->typeNameHTML = $documentTypeNamesForHTML[$row->typeId];
        }

        $section->groupId = $row->groupId;
        $section->typeId = $row->typeId;
        $section->archiveIds = new ArrayObject();

        $sections[] = $section;
    }

    $section->archiveIds->append($row->archiveId);
}

$result->free();

?>

<nav style="text-align: center; margin-bottom: 20px;">
    <?php renderDocumentsAdministrationMenu(); ?>
</nav>

<main>
    <style scoped>

        section.group-type a
        {
            margin: 5px 15px;
        }

        section.group-type .hash
        {
            font-size: 0.55em;
        }

        section.group-type header
        {
            margin-bottom: 10px;
        }

        section.group-type header h1
        {
            margin-top: 5px;
            font-size: 1em;
        }

        section.group-type header h2
        {
            margin-top: 5px;
            color: #3f3f3f;
            font-size: 0.8em;
        }

        section.group-type header .hash
        {
            margin-top: 2px;
            color: #7f7f7f;
        }

        section.group-type + section.group-type
        {
            margin-top: 30px;
        }

    </style>

    <?php PagesSummaryView::renderAsHTML(); ?>

    <h1>ColbyPages Rows with a NULL `className`</h1>

    <?php

    foreach ($sections as $section)
    {
        ?>

        <section class="group-type">
            <header>
                <h1>Group: <?php echo $section->groupNameHTML; ?></h1>
                <div class="hash"><?php echo $section->groupId; ?></div>
                <h1>Type: <?php echo $section->typeNameHTML; ?></h1>
                <div class="hash"><?php echo $section->typeId; ?></div>
                <h2>Count: <?php echo $section->archiveIds->count(); ?></h2>
            </header>

            <?php

            foreach ($section->archiveIds as $archiveId)
            {
                echo linkForArchiveId($archiveId), "\n";
            }

            ?>

        </section>

        <?php
    }

    ?>

</main>

<?php

$footer = CBAdminPageFooterView::init();
$footer->renderHTML();

CBHTMLOutput::render();

/**
 * @return void
 */
class PagesSummaryView {

    /**
     * @return void
     */
    public static function renderAsHTML($renderModel = null) {

        ?>

        <table id="overview">
            <style>

                #overview {
                    margin: 20px auto;
                }

                #overview td {
                    padding: 2px 5px;
                }

                #overview td + td {
                    text-align: right;
                }

            </style>

        <?php

        $SQL = <<<EOT

            SELECT
                `className`,
                count(*) AS `count`
            FROM
                `ColbyPages`
            WHERE
                `className` IS NOT NULL
            GROUP BY
                `className`

EOT;

        $result = Colby::query($SQL);

        while ($row = $result->fetch_object()) {
            echo "<tr><td>className: {$row->className}</td><td>{$row->count}</td></tr>";
        }

        $result->free();

        $SQL = <<<EOT

            SELECT
                HEX(`typeID`) as `typeID`,
                count(*) as `count`
            FROM
                `ColbyPages`
            WHERE
                `className` IS NULL
            GROUP BY
                `typeID`

EOT;

        $result = Colby::query($SQL);

        while ($row = $result->fetch_object()) {
            $typeID = ($row->typeID === null) ? 'NULL' : $row->typeID;
            echo "<tr><td>typeID: {$typeID}</td><td>{$row->count}</td></tr>";
        }

        $result->free();

        echo '</table>';
    }
}

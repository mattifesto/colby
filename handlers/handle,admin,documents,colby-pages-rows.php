<?php

if (!ColbyUser::current()->isOneOfThe('Administrators')) {
    return include CBSystemDirectory . '/handlers/handle-authorization-failed.php';
}

include_once COLBY_SYSTEM_DIRECTORY . '/snippets/shared/documents-administration.php';

CBHTMLOutput::$classNameForSettings = 'CBPageSettingsForAdminPages';
CBHTMLOutput::begin();
CBHTMLOutput::setTitleHTML('ColbyPages Table Rows');
CBHTMLOutput::setDescriptionHTML('Pages that are in the ColbyPages table.');

$spec                           = new stdClass();
$spec->selectedMenuItemName     = 'develop';
$spec->selectedSubmenuItemName  = 'documents';
CBAdminPageMenuView::renderModelAsHTML(CBAdminPageMenuView::specToModel($spec));

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
            $section->groupNameHTML = 'Unknown';
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
            $section->typeNameHTML = 'Unknown';
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

    <?php PagesSummaryView::renderAsHTML(); UnpublishedPagesWithURIsView::renderModelAsHTML(); ?>

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

CBAdminPageFooterView::renderModelAsHTML();

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
                    background-color: hsl(30, 30%, 95%);
                    border: 1px solid hsl(30, 30%, 80%);
                    margin: 20px auto;
                }

                #overview td, #overview th {
                    padding: 2px 5px;
                }

                #overview td:last-child, #overview th:last-child {
                    text-align: right;
                }

            </style>
            <thead>
                <tr><th>className</th><th>classNameForKind</th><th>count</th><tr>
            </thead>

        <?php

        $SQL = <<<EOT

            SELECT      `className`,
                        `classNameForKind`,
                        count(*) AS `count`
            FROM        `ColbyPages`
            WHERE       `className` IS NOT NULL OR `classNameForKind` IS NOT NULL
            GROUP BY    `className`, `classNameForKind`

EOT;

        $result = Colby::query($SQL);

        while ($row = $result->fetch_object()) {
            echo "<tr><td>{$row->className}</td><td>{$row->classNameForKind}</td><td>{$row->count}</td></tr>";
        }

        $result->free();

        $SQL = <<<EOT

            SELECT      LOWER(HEX(`typeID`)) as `typeID`,
                        count(*) as `count`
            FROM        `ColbyPages`
            WHERE       `className` IS NULL AND `classNameForKind` IS NULL
            GROUP BY    `typeID`

EOT;

        $result = Colby::query($SQL);

        while ($row = $result->fetch_object()) {
            $typeID = ($row->typeID === null) ? 'NULL' : $row->typeID;
            echo "<tr><td colspan=\"2\">typeID: {$typeID}</td><td>{$row->count}</td></tr>";
        }

        $result->free();

        echo '</table>';
    }
}

final class UnpublishedPagesWithURIsView {

    public static function renderModelAsHTML(stdClass $model = null) {
        $SQL = <<<EOT

            SELECT  LOWER(HEX(`archiveID`)), `className`, `classNameForKind`, `titleHTML`, `URI`
            FROM    `ColbyPages`
            WHERE   `published` IS NULL AND
                    `URI` IS NOT NULL

EOT;

        $pages = CBDB::SQLToObjects($SQL);

        if (empty($pages)) {
            return;
        }

        ?>

        <script>
            "use strict";

            function cleanUnpublishedPages() {
                var xhr     = new XMLHttpRequest();
                xhr.onload  = cleanUnpublishedPagesDidComplete.bind(undefined, xhr);

                xhr.open("POST", "/api/?class=CBPagesMaintenance&function=cleanUnpublishedPages");
                xhr.send();
            }

            function cleanUnpublishedPagesDidComplete(xhr) {
                var response = Colby.responseFromXMLHttpRequest(xhr);

                Colby.displayResponse(response);
            }
        </script>
        <div style="text-align: center; margin: 10px 0 5px;">Unpublished Pages with URIs</div>
        <div style="text-align: center; margin: 5px 0;">
            <button onclick="cleanUnpublishedPages();">Clean Unpublished Pages</button>
        </div>
        <table id="unpublished">
            <style>

                #unpublished {
                    background-color:   hsl(30, 30%, 95%);
                    border:             1px solid hsl(30, 30%, 80%);
                    font-size:          12px;
                    margin:             0px auto 20px;
                    width:              960px;
                }

                #unpublished td, #overview th {
                    padding: 2px 5px;
                }

                #unpublished td:first-child {
                    font-family:    monospace;
                    font-size:      10px;
                }

                #unpublished td:last-child {
                    white-space: nowrap;
                }

            </style>

            <?php

            array_walk($pages, function($page) {
                $values = array_values((array)$page);
                $values = array_map(function($value) {
                    $valueAsHTML = ColbyConvert::textToHTML($value);
                    return "<td>{$valueAsHTML}</td>";
                }, $values);
                $values = implode('', $values);
                echo "<tr>{$values}</tr>";
            });

            ?>

        </table>

        <?php
    }
}

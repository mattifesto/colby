<?php

if (!ColbyUser::current()->isOneOfThe('Administrators')) {
    return include CBSystemDirectory . '/handlers/handle-authorization-failed.php';
}

include_once CBSystemDirectory . '/snippets/shared/documents-administration.php';

CBHTMLOutput::$classNameForSettings = 'CBPageSettingsForAdminPages';
CBHTMLOutput::begin();
CBHTMLOutput::setTitleHTML('ColbyPages Table Rows');
CBHTMLOutput::setDescriptionHTML('Pages that are in the ColbyPages table.');
CBHTMLOutput::addCSSURL(CBSystemURL . '/handlers/handle,admin,documents,colby-pages-rows.css');

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
    <div class="summaryLists">

        <?php

        CBPagesTableSummaryView::renderModelAsHTML((object)['type' => 'published']);
        CBPagesTableSummaryView::renderModelAsHTML((object)['type' => 'unpublished']);

        ?>

    </div>

    <?php UnpublishedPagesWithURIsView::renderModelAsHTML(); ?>

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
 *
 */
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
            <button onclick="cleanUnpublishedPages();">Remove URIs from Unpublished Pages</button>
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

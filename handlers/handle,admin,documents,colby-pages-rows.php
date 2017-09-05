<?php

if (!ColbyUser::current()->isOneOfThe('Administrators')) {
    return include CBSystemDirectory . '/handlers/handle-authorization-failed.php';
}

CBHTMLOutput::$classNameForSettings = 'CBPageSettingsForAdminPages';
CBHTMLOutput::begin();
CBHTMLOutput::setTitleHTML('ColbyPages Table Rows');
CBHTMLOutput::setDescriptionHTML('Pages that are in the ColbyPages table.');
CBHTMLOutput::requireClassName('CBUI');
CBHTMLOutput::addCSSURL(CBSystemURL . '/handlers/handle,admin,documents,colby-pages-rows.css');

CBView::render((object)[
    'className' => 'CBAdminPageMenuView',
    'selectedMenuItemName' => 'develop',
    'selectedSubmenuItemName' => 'documents',
]);

?>

<main class="CBUIRoot CBPagesRows">
    <div class="summaryLists">

        <?php

        CBView::render((object)[
            'className' => 'CBPagesTableSummaryView',
            'type' => 'published'
        ]);

        CBView::render((object)[
            'className' => 'CBPagesTableSummaryView',
            'type' => 'unpublished'
        ]);

        ?>

    </div>

    <?php

    CBView::render((object)[
        'className' => 'UnpublishedPagesWithURIsView',
    ]);

    $SQL = <<< EOT

        SELECT  HEX(`archiveID`) as `ID`
        FROM    `ColbyPages`
        WHERE   `className` IS NULL

EOT;

    $IDs = CBDB::SQLToArray($SQL);

    if (count($IDs) > 0) {

        echo '<section><h1>ColbyPages Rows with a NULL `className`</h1><div>';

        foreach ($IDs as $ID) {
            echo linkForArchiveId($ID), "\n";
        }

        echo '</div></section>';
    }

    ?>

</main>

<?php

CBView::render((object)[
    'className' => 'CBAdminPageFooterView',
]);

CBHTMLOutput::render();

/**
 *
 */
final class UnpublishedPagesWithURIsView {

    static function CBView_render(stdClass $model = null) {
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

/**
 * @return string
 */
function linkForArchiveId($archiveId) {
    $href = CBSitePreferences::siteURL() . "/admin/documents/view/?archive-id={$archiveId}";

    return "<a href=\"{$href}\"><span class=\"hash\">{$archiveId}</span></a>";
}

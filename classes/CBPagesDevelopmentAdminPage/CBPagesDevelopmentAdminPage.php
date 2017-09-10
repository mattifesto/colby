<?php

final class CBPagesDevelopmentAdminPage {

    /**
     * @return [string]
     */
    static function adminPageMenuNamePath() {
        return ['develop', 'pages'];
    }

    /**
     * @return stdClass
     */
    static function adminPagePermissions() {
        return (object)['group' => 'Developers'];
    }

    /**
     * @return null
     */
    static function adminPageRenderContent() {
        CBHTMLOutput::setTitleHTML('CBPages Development Information');
        CBHTMLOutput::setDescriptionHTML('Pages that are in the ColbyPages table.');

        ?>

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

            SELECT  LOWER(HEX(`archiveID`)) as `ID`
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
    }

    /**
     * @return string
     */
    static function CBHTMLOutput_CSSURLs() {
        return [Colby::flexpath(__CLASS__, 'css', cbsysurl())];
    }
}

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
        <div id="unpublished">
            <style>

                #unpublished {
                    display: flex;
                    justify-content: center;
                    padding: 20px;
                }

                #unpublished > div {
                    background-color: var(--CBBackgroundColor);
                    border: 1px solid var(--CBLineColor);
                    border-radius: 5px;
                }

                #unpublished .record {
                    padding: 10px;
                }

            </style>
            <div>

                <?php

                array_walk($pages, function($page) {
                    $values = array_values((array)$page);
                    $values = array_map(function($value) {
                        $valueAsHTML = ColbyConvert::textToHTML($value);
                        return "<div>{$valueAsHTML}</div>";
                    }, $values);
                    $values = implode('', $values);
                    echo "<div class=\"record\">{$values}</div>";
                });

                ?>

            </div>
        </div>

        <?php
    }
}

/**
 * @return string
 */
function linkForArchiveId($archiveId) {
    $href = CBSitePreferences::siteURL() . "/admin/page/?class=CBDataStoreAdminPage&ID={$archiveId}";

    return "<a href=\"{$href}\"><span class=\"hash\">{$archiveId}</span></a>";
}

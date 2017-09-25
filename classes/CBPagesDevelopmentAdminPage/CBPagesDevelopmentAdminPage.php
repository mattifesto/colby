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
                $href = cbsiteurl() . "/admin/page/?class=CBDataStoreAdminPage&ID={$ID}";
                echo "<a href=\"{$href}\"><span class=\"hash\">{$ID}</span></a>\n";
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

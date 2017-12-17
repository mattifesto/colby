<?php

final class CBPagesDevelopmentAdminPage {

    /**
     * @return [string]
     */
    static function adminPageMenuNamePath() {
        return ['pages', 'develop'];
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

        <div class="CBPagesDevelopmentAdminPage_content"></div>
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

            SELECT  LOWER(HEX(`archiveID`)) as `ID`, `titleHTML`
            FROM    `ColbyPages`
            WHERE   `className` IS NULL

EOT;

        $rows = CBDB::SQLToObjects($SQL);

        if (count($rows) > 0) {
            $links = array_map(function ($row) {
                $title = (trim($row->titleHTML) === '') ? $row->ID : $row->titleHTML;
                $text = CBMessageMarkup::stringToMarkup($title);
                $URL = CBMessageMarkup::stringToMarkup(cbsiteurl() . "/admin/?c=CBModelInspector&ID={$row->ID}");

                return "({$text} (a $URL))";
            }, $rows);

            $message = "ColbyPages rows where `className` is NULL\n\n--- ul\n" .
                       implode("\n\n", $links) .
                       "\n---";

            $message = cbhtml(json_encode($message));

            ?>

            <div class="CBUIExpander_builder" data-message="<?= $message ?>"></div>

            <?php

            /*foreach ($IDs as $ID) {
                $href = cbsiteurl() . "/admin/?c=CBModelInspector&ID={$ID}";
                echo "<a href=\"{$href}\"><span class=\"hash\">{$ID}</span></a>\n";
            }*/

        }
    }

    /**
     * @return [string]
     */
    static function CBHTMLOutput_CSSURLs() {
        return [Colby::flexpath(__CLASS__, 'css', cbsysurl())];
    }

    /**
     * @return [string]
     */
    static function CBHTMLOutput_JavaScriptURLs() {
        return [Colby::flexpath(__CLASS__, 'js', cbsysurl())];
    }

    /**
     * @return [string]
     */
    static function CBHTMLOutput_requiredClassNames() {
        return ['CBUIExpander'];
    }
}

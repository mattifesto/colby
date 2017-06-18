<?php

/**
 * This class renders a page that shows information related to any hex160 ID.
 */
final class CBAdminPageForID {

    /**
     * @return null
     */
    static function renderCBModelsInformation($ID) {
        $data = CBModels::fetchSpecAndModelByID($ID);

        if ($data === false) {
            return;
        }

        ?>

        <section class="CBModels">
            <h1>CBModels Table</h1>
            <?php

            CBAdminPageForID::renderOpener('Spec', function () use($data) {
                return '<div class="JSON">' .
                    cbhtml(json_encode($data->spec, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES)) .
                    '</div>';
            });

            CBAdminPageForID::renderOpener('Model', function () use($data) {
                return '<div class="JSON">' .
                    cbhtml(json_encode($data->model, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES)) .
                    '</div>';
            });

            ?>
        </section>

        <?php

        echo '</section>';
    }

    /**
     * @retrun null
     */
    static function renderCBImagesInformation($ID) {
        $IDAsSQL = CBHex160::toSQL($ID);
        $SQL = <<<EOT

            SELECT  `created`, `modified`, `extension`
            FROM    `CBImages`
            WHERE   `ID` = {$IDAsSQL}

EOT;

        $data = CBDB::SQLToObject($SQL);

        if ($data) {
            echo '<section class="CBImages"><h1>CBImages Table</h1>';

            foreach ($data as $name => $value) {
                $type = null;

                if ('published' == $name || 'created' == $name || 'modified' == $name) {
                    $type = 'time';
                } else if ('thumbnailURL' == $name) {
                    $type = 'URI';
                }

                displayKeyValuePair($name, $value, $type);
            }

            echo '</section>';
        }
    }

    static function renderOpener($title, callable $renderContent) {
        ?>

        <div class="CBUIOpener">
            <div class="title"><?= cbhtml($title); ?> <span class="button" onclick="CBAdminPageForID.toggleOpener(event)"></span></div>
            <div class="content"><?= $renderContent() ?></div>
        </div>

        <?php
    }

    /**
     * @return [string]
     */
    static function requiredCSSURLs() {
        return [Colby::flexnameForCSSForClass(CBSystemURL, __CLASS__)];
    }

    /**
     * @return [string]
     */
    static function requiredJavaScriptURLs() {
        return [Colby::flexnameForJavaScriptForClass(CBSystemURL, __CLASS__)];
    }
}

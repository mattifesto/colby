<?php

final class CBModelsAdmin {

    static $page = '';

    static function CBAdmin_initialize(): void {
        CBModelsAdmin::$page = cb_query_string_value('p', '');
        error_log(CBModelsAdmin::$page);
    }

    /**
     * @return [string]
     */
    static function CBAdmin_menuNamePath(): array {
        switch (CBModelsAdmin::$page) {
            case 'list':
                return ['models'];

            default:
                return ['models', 'directory'];
        }
    }

    /**
     * @return void
     */
    static function CBAdmin_render(): void {
        switch (CBModelsAdmin::$page) {
            case 'list':
                break;

            default:
                CBModelsAdmin::renderDirectory();
                break;
        }
    }

    /**
     * @return [string]
     */
    static function CBHTMLOutput_requiredClassNames() {
        return ['CBUI'];
    }

    /**
     * @return [string]
     */
    static function CBHTMLOutput_JavaScriptURLs() {
        return [Colby::flexpath(__CLASS__, 'v359.js', cbsysurl())];
    }

    /**
     * @return [[<name>, <value>]]
     */
    static function CBHTMLOutput_JavaScriptVariables() {
        $variables = [
            ['CBModelsAdmin_page', CBModelsAdmin::$page],
        ];

        switch (CBModelsAdmin::$page) {
            case 'list':
                $variables[] = ['CBModelsAdmin_modelClassName', cb_query_string_value('modelClassName')];
                $variables[] = ['CBModelsAdmin_modelList', CBModelsAdmin::fetchModelList()];
                break;

            default:
                break;
        }

        return $variables;
    }

    /**
     * @return [object]
     */
    private static function fetchModelList() {
        $modelClassNameAsSQL = CBDB::stringToSQL(cb_query_string_value('modelClassName'));
        $SQL = <<<EOT

            SELECT  LOWER(HEX(`ID`)) AS `ID`, `title`
            FROM    `CBModels`
            WHERE   `className` = {$modelClassNameAsSQL}

EOT;

        return CBDB::SQLToObjects($SQL);
    }

    /**
     * @return void
     */
    private static function renderDirectory(): void {
        CBHTMLOutput::setTitleHTML('Models Directory');

        $classNames = CBDB::SQLToArray('SELECT DISTINCT `className` FROM `CBModels`');
        $classNames = array_merge($classNames, CBModelsPreferences::classNamesOfEditableModels());
        $classNames = array_values(array_unique($classNames));

        sort($classNames);

        $items = array_map(function ($className) {
            $item = (object)[
                'titleAsHTML' => cbhtml($className),
            ];

            if (defined("{$className}::ID")) {
                $ID = constant("{$className}::ID");
                $item->href = "/admin/page/?class=CBAdminPageForEditingModels&ID={$ID}";
            } else {
                $item->href = "/admin/?c=CBModelsAdmin&p=list&modelClassName={$className}";
            }

            return $item;
        }, $classNames);

        CBUI::renderHalfSpace();

        ?>

        <div class="CBUISection">
            <?php foreach ($items as $item) { ?>
                <div class="CBUISectionItem components"
                     onclick="window.location = '<?= $item->href ?>';">
                    <div class="ellipsisTextContainer">
                        <div class="ellipsisText">
                            <?= $item->titleAsHTML ?>
                        </div>
                    </div>
                    <div class="arrow">
                    </div>
                </div>
            <?php } ?>
        </div>

        <?php

        CBUI::renderHalfSpace();
    }
}

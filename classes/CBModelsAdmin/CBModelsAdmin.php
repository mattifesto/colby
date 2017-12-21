<?php

final class CBModelsAdmin {

    /**
     * @return [string]
     */
    static function CBAdmin_menuNamePath(): array {
        return ['models', 'directory'];
    }

    /**
     * @return void
     */
    static function CBAdmin_render(): void {
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
                $item->href = "/admin/page/?class=CBAdminPageForModelList&modelClassName={$className}";
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

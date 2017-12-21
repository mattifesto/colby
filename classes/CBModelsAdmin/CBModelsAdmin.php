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

        $items = [];

        foreach (CBModelsPreferences::classNamesOfEditableModels() as $className) {
            if (!class_exists($className)) {
                continue;
            }

            $item = new stdClass();

            if (defined("{$className}::ID")) {
                $ID = constant("{$className}::ID");
                $item->href = "/admin/page/?class=CBAdminPageForEditingModels&ID={$ID}";
            } else {
                $item->href = "/admin/page/?class=CBAdminPageForModelList&modelClassName={$className}";
            }

            $item->titleAsHTML = $className;
            $items[$className] = $item;
        }

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

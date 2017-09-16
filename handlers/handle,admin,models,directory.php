<?php

if (!ColbyUser::current()->isOneOfThe('Administrators')) {
    return include CBSystemDirectory . '/handlers/handle-authorization-failed.php';
}

CBHTMLOutput::$classNameForSettings = 'CBPageSettingsForAdminPages';
CBHTMLOutput::begin();
CBHTMLOutput::setTitleHTML('Model Directory');
CBHTMLOutput::setDescriptionHTML('A list of model classes');
CBHTMLOutput::requireClassName('CBUI');
CBHTMLOutput::addCSSURL(CBSystemURL . '/handlers/handle,admin,models,directory.css');

CBView::render((object)[
    'className' => 'CBAdminPageMenuView',
    'selectedMenuItemName' => 'models',
    'selectedSubmenuItemName' => 'directory',
]);

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

?>

<div class="CBUIRoot">
    <div class="CBUIHalfSpace"></div>
    <div class="CBUISection">
        <?php foreach ($items as $item) { ?>
            <div class="CBUISectionItem CBModelClassSectionItem"
                 onclick="window.location = '<?= $item->href ?>';">
                <?= $item->titleAsHTML ?>
            </div>
        <?php } ?>
    </div>
    <div class="CBUIHalfSpace"></div>
</div>

<?php

CBView::render((object)[
    'className' => 'CBAdminPageFooterView',
]);

CBHTMLOutput::render();

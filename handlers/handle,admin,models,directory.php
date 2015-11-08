<?php

if (!ColbyUser::current()->isOneOfThe('Administrators')) {
    return include CBSystemDirectory . '/handlers/handle-authorization-failed.php';
}

CBHTMLOutput::$classNameForSettings = 'CBPageSettingsForAdminPages';
CBHTMLOutput::begin();
CBHTMLOutput::setTitleHTML('Model Directory');
CBHTMLOutput::setDescriptionHTML('A list of model classes');
CBHTMLOutput::addCSSURL(CBSystemURL . '/css/CBUI.css');
CBHTMLOutput::addCSSURL(CBSystemURL . '/handlers/handle,admin,models,directory.css');

$spec = (object)['selectedMenuItemName' => 'models'];
CBAdminPageMenuView::renderModelAsHTML(CBAdminPageMenuView::specToModel($spec));

$items = [];

foreach (CBHandleAdminModelsDirectory::classMenuItems() as $menuItem) {
    $info = CBModelClassInfo::classNameToInfo($menuItem->itemClassName);

    if (!ColbyUser::current()->isOneOfThe($info->userGroup)) {
        continue;
    }

    $item = new stdClass();

    if (defined("{$menuItem->itemClassName}::ID")) {
        $ID = constant("{$menuItem->itemClassName}::ID");
        $item->href = "/admin/models/edit/?ID={$ID}";
    } else {
        $item->href = "/admin/models/list/?class={$menuItem->itemClassName}";
    }

    if (is_callable($function = "{$menuItem->itemClassName}::info")) {
        $info = call_user_func($function);
        $item->titleAsHTML = $info->pluralTitleAsHTML;
    } else {
        $item->titleAsHTML = $menuItem->itemClassName;
    }

    $items[$menuItem->itemClassName] = $item;
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

CBAdminPageFooterView::renderModelAsHTML();
CBHTMLOutput::render();

/**
 *
 */
final class CBHandleAdminModelsDirectory {

    /**
     * @return [{CBClassMenuItem}]
     */
    public static function classMenuItems() {
        $model      = CBModels::fetchModelByID(CBModelsPreferences::ID);
        $menuItems  = array_filter($model->classMenuItems, function($menuItem) {
            if (empty($menuItem->group) || ColbyUser::current()->isOneOfThe($menuItem->group)) {
                return true;
            } else {
                return false;
            }
        });

        return $menuItems;
    }
}

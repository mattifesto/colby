<?php

if (!ColbyUser::current()->isOneOfThe('Administrators')) {
    return include CBSystemDirectory . '/handlers/handle-authorization-failed.php';
}

CBHTMLOutput::$classNameForSettings = 'CBPageSettingsForAdminPages';
CBHTMLOutput::begin();
CBHTMLOutput::setTitleHTML('Edit');
CBHTMLOutput::setDescriptionHTML('Edit models');
CBHTMLOutput::addCSSURL(CBSystemURL . '/css/CBUI.css');
CBHTMLOutput::addCSSURL(CBSystemURL . '/handlers/handle,admin,models,directory.css');

$spec = (object)['selectedMenuItemName' => 'edit'];
CBAdminPageMenuView::renderModelAsHTML(CBAdminPageMenuView::specToModel($spec));

?>

<div class="CBUIRoot">
    <div class="CBUIHalfSpace"></div>
    <div class="CBUISection">
        <?php foreach (CBHandleAdminModelsDirectory::classMenuItems() as $menuItem) { ?>
            <div class="CBUISectionItem CBModelClassSectionItem"
                 onclick="window.location = '/admin/models/list/?class=<?= $menuItem->itemClassName ?>';">
                <?= ColbyConvert::textToHTML($menuItem->title); ?>
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

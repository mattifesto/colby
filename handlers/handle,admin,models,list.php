<?php

if (!ColbyUser::current()->isOneOfThe('Administrators')) {
    return include CBSystemDirectory . '/handlers/handle-authorization-failed.php';
}

if (!isset($_GET['class'])) {
    include Colby::findFile('handlers/handle-default.php');
    exit;
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
        <?php foreach (CBHandleAdminModelsList::infoForModels() as $infoForModel) { ?>
            <div class="CBUISectionItem CBModelClassSectionItem"
                 onclick="window.location = '/admin/models/edit/?ID=<?= $infoForModel->ID ?>';">
                <?= ColbyConvert::textToHTML($infoForModel->title); ?>
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
final class CBHandleAdminModelsList {

    /**
     * @return [{CBClassMenuItem}]
     */
    public static function infoForModels() {
        $className = $_GET['class'];
        $classNameAsSQL = CBDB::stringToSQL($className);
        $SQL = <<<EOT

            SELECT      LOWER(HEX(`ID`)) AS `ID`, `className`, `created`, `modified`, `title`
            FROM        `CBModels`
            WHERE       `className` = {$classNameAsSQL}
            ORDER BY    `className`, `modified` DESC

EOT;

        return CBDB::SQLToObjects($SQL);
    }
}

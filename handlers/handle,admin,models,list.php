<?php

if (!ColbyUser::current()->isOneOfThe('Administrators')) {
    return include CBSystemDirectory . '/handlers/handle-authorization-failed.php';
}

if (!isset($_GET['class'])) {
    include Colby::findFile('handlers/handle-default.php');
    exit;
}

$classNameForModels = $_GET['class'];

if (is_callable($function = "{$classNameForModels}::info")) {
    $info = call_user_func($function);
    $title = $info->pluralTitle;
    $titleAsHTML = $info->pluralTitleAsHTML;
} else {
    $title = $classNameForModels;
    $titleAsHTML = ColbyConvert::textToHTML($title);
}


CBHTMLOutput::$classNameForSettings = 'CBPageSettingsForAdminPages';
CBHTMLOutput::begin();
CBHTMLOutput::setTitleHTML("{$titleAsHTML}");
CBHTMLOutput::setDescriptionHTML("A list of {$titleAsHTML}");
CBHTMLOutput::addCSSURL(CBSystemURL . '/css/CBUI.css');
CBHTMLOutput::addCSSURL(CBSystemURL . '/handlers/handle,admin,models,list.css');

$spec = (object)['selectedMenuItemName' => 'models'];
CBAdminPageMenuView::renderModelAsHTML(CBAdminPageMenuView::specToModel($spec));

?>

<div class="CBUIRoot">
    <div class="CBUIHeader">
        <div class="left"></a></div>
        <div class="center"><?= $titleAsHTML ?></div>
        <div class="right">
            <div class="CBUIHeaderAction" onclick="CBHandleAdminModelsList.handleNewClicked();">New</div>
        </div>
    </div>
    <div class="CBUIHalfSpace"></div>
    <div class="CBUISection">
        <?php foreach (CBHandleAdminModelsList::infoForModels() as $infoForModel) { ?>
            <div class="CBUISectionItem"
                 onclick="window.location = '/admin/models/edit/?ID=<?= $infoForModel->ID ?>';">
                <?= ColbyConvert::textToHTML($infoForModel->title); ?>
            </div>
        <?php } ?>
    </div>
    <div class="CBUIHalfSpace"></div>
</div>

<?php

CBAdminPageFooterView::renderModelAsHTML();

?>

<script>
"use strict";

var CBHandleAdminModelsList = {

    /**
     * @return  undefined
     */
    handleNewClicked : function(args) {
        window.location.href = "/admin/models/edit/?className=" + <?= json_encode($classNameForModels) ?>;
    },
};

</script>

<?php

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

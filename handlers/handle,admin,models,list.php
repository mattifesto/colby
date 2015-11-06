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

$models = CBHandleAdminModelsList::fetchModelsByClassName($classNameForModels);

if (is_callable($function = "{$classNameForModels}::compareModels")) {
    if (!uasort($models, $function)) {
        throw new RuntimeException('Sorting the models failed.');
    }
}

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
        <?php foreach ($models as $model) { ?>
            <div class="CBUISectionItem"
                 onclick="window.location = '/admin/models/edit/?ID=<?= $model->ID ?>';">
                <?= ColbyConvert::textToHTML($model->title); ?>
                <div class="information"><?php
                    if (is_callable($function = "{$classNameForModels}::modelToSummaryText")) {
                        echo cbhtml(call_user_func($function, $model));
                    }
                ?></div>
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
     * This function can be moved into CBModels if it proves to be useful in
     * other contexts.
     *
     * @param {string} $className
     *
     * @return [{hex160} => {stdClass}]
     */
    public static function fetchModelsByClassName($className) {
        $classNameAsSQL = CBDB::stringToSQL($className);
        $SQL = <<<EOT

            SELECT  LOWER(HEX(`m`.`ID`)), `v`.`modelAsJSON`
            FROM    `CBModels` AS `m`
            JOIN    `CBModelVersions` AS `v` ON `m`.`ID` = `v`.`ID` AND `m`.`version` = `v`.`version`
            WHERE   `m`.`className` = {$classNameAsSQL}

EOT;

        return CBDB::SQLToArray($SQL, ['valueIsJSON' => true]);
    }
}

<?php

$page = new ColbyOutputManager('admin-html-page');

$page->titleHTML = 'View Editor';
$page->descriptionHTML = 'Edit the attributes of a view.';

$page->begin();

if (!ColbyUser::current()->isOneOfThe('Developers'))
{
    include Colby::findSnippet('authenticate.php');

    goto done;
}

if (isset($_GET['view-id']))
{
    $viewId = $_GET['view-id'];
}
else
{
    $viewId = sha1(microtime() . rand());

    header("Location: {$_SERVER['REQUEST_URI']}?view-id={$viewId}");
}

$dataFilename = "handle,admin,view,{$viewId}.data";

$absoluteDataFilename = Colby::findHandler($dataFilename);

if ($absoluteDataFilename)
{
    $data = unserialize(file_get_contents($absoluteDataFilename));
}

$ajaxURL = COLBY_SITE_URL . '/developer/views/ajax/update/';
$nameHTML = isset($data->nameHTML) ? $data->nameHTML : '';
$descriptionHTML = isset($data->description) ? ColbyConvert::textToHTML($data->description) : '';
$selectedModelId = isset($data->modelId) ? $data->modelId : '';
$selectedGroupId = isset($data->groupId) ? $data->groupId : '';

$modelDataFiles = glob(COLBY_SITE_DIRECTORY . '/colby/handlers/handle,admin,model,*.data');
$modelDataFiles = array_merge($modelDataFiles,
                              glob(COLBY_SITE_DIRECTORY . '/handlers/handle,admin,model,*.data'));

$models = array();

foreach ($modelDataFiles as $modelDataFile)
{
    preg_match('/([0-9a-f]{40})/', $modelDataFile, $matches);

    $modelId = $matches[1];

    $models[$modelId] = unserialize(file_get_contents($modelDataFile));
}

$groupDataFiles = glob(COLBY_SITE_DIRECTORY . '/colby/handlers/handle,admin,group,*.data');
$groupDataFiles = array_merge($groupDataFiles,
                              glob(COLBY_SITE_DIRECTORY . '/handlers/handle,admin,group,*.data'));

$groups = array();

foreach ($groupDataFiles as $groupDataFile)
{
    preg_match('/([0-9a-f]{40})/', $groupDataFile, $matches);

    $groupId = $matches[1];

    $groups[$groupId] = unserialize(file_get_contents($groupDataFile));
}

?>

<fieldset>
    <progress value="0"
              style="width: 100px; float: right;"></progress>

    <div><label>View Id
        <input type="text"
               id="view-id"
               value="<?php echo $viewId; ?>"
               style="font-family: monospace;"
               disabled>
    </label></div>

    <div><label>Name
        <input type="text"
               id="name"
               value="<?php echo $nameHTML; ?>">
    </label></div>

    <div><label>Description
        <textarea id="description"
                  style="height: 300px;"><?php echo $descriptionHTML; ?></textarea>
    </label></div>

    <div><label>Model
        <select id="model-id">
            <?php

            foreach ($models as $modelId => $modelData)
            {
                $selected = ($selectedModelId == $modelId) ? ' selected="selected"' : '';

                echo "<option value=\"{$modelId}\"{$selected}>{$modelData->nameHTML}</option>\n";
            }

            ?>
        </select>
    </div>

    <div><label>Group
        <select id="group-id">
            <?php

            foreach ($groups as $groupId => $groupData)
            {
                $selected = ($selectedGroupId == $groupId) ? ' selected="selected"' : '';

                echo "<option value=\"{$groupId}\"{$selected}>{$groupData->nameHTML}</option>\n";
            }

            ?>
        </select>
    </div>

    <div id="error-log"></div>
</fieldset>

<script>
"use strict";

var formManager;

function handleContentLoaded()
{
    formManager = new ColbyFormManager('<?php echo $ajaxURL; ?>');
}

document.addEventListener('DOMContentLoaded', handleContentLoaded, false);

</script>

<?php

done:

$page->end();

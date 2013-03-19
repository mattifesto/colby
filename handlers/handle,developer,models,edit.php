<?php

$page = new ColbyOutputManager('admin-html-page');

$page->titleHTML = 'Model Editor';
$page->descriptionHTML = 'Edit the attributes of a model.';

$page->begin();

if (!ColbyUser::current()->isOneOfThe('Developers'))
{
    include Colby::findSnippet('authenticate.php');

    goto done;
}

if (isset($_GET['model-id']))
{
    $modelId = $_GET['model-id'];
}
else
{
    $modelId = sha1(microtime() . rand());

    header("Location: {$_SERVER['REQUEST_URI']}?model-id={$modelId}");
}

$dataFilename = "handle,admin,model,{$modelId}.data";

$absoluteDataFilename = Colby::findHandler($dataFilename);

if ($absoluteDataFilename)
{
    $data = unserialize(file_get_contents($absoluteDataFilename));
}

$ajaxURL = COLBY_SITE_URL . '/developer/models/ajax/update/';
$nameHTML = isset($data->nameHTML) ? $data->nameHTML : '';
$descriptionHTML = isset($data->description) ? ColbyConvert::textToHTML($data->description) : '';

?>

<fieldset>
    <progress value="0"
              style="width: 100px; float: right;"></progress>

    <div><label>Model Id
        <input type="text"
               id="model-id"
               value="<?php echo $modelId; ?>"
               readonly="readonly"
               style="font-family: monospace;">
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

<?php

$page = ColbyOutputManager::beginVerifiedUserPage('Group Editor',
                                                  'Edit the attributes of a group.',
                                                  'admin');

if (isset($_GET['group-id']))
{
    $groupId = $_GET['group-id'];
}
else
{
    $groupId = sha1(microtime() . rand());

    header("Location: {$_SERVER['REQUEST_URI']}?group-id={$groupId}");
}

$dataFilename = "handle,admin,group,{$groupId}.data";

$absoluteDataFilename = Colby::findHandler($dataFilename);

if ($absoluteDataFilename)
{
    $data = unserialize(file_get_contents($absoluteDataFilename));
}

$ajaxURL = COLBY_SITE_URL . '/developer/groups/ajax/update/';
$nameHTML = isset($data->nameHTML) ? $data->nameHTML : '';
$stub = isset($data->stub) ? $data->stub : '';
$descriptionHTML = isset($data->description) ? ColbyConvert::textToHTML($data->description) : '';

?>

<fieldset>
    <progress value="0"
              style="width: 100px; float: right;"></progress>

    <div><label>Group Id
        <input type="text"
               id="group-id"
               value="<?php echo $groupId; ?>"
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

    <div><label>Stub
        <input type="text"
               id="stub"
               value="<?php echo $stub; ?>">
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

$page->end();

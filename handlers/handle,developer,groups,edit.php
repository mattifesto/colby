<?php

$page = new ColbyOutputManager('admin-html-page');

$page->titleHTML = 'Group Editor';
$page->descriptionHTML = 'Edit the attributes of a group.';

$page->begin();

if (!ColbyUser::current()->isOneOfThe('Developers'))
{
    include Colby::findSnippet('authenticate.php');

    goto done;
}

$location = $_GET['location'];

if (isset($_GET['group-id']))
{
    $groupId = $_GET['group-id'];
}
else
{
    $groupId = sha1(microtime() . rand());
    $uriParts = explode('?', $_SERVER['REQUEST_URI']);

    header("Location: {$uriParts[0]}?location={$location}&group-id={$groupId}");
}

$groupDirectory = COLBY_SITE_DIRECTORY . "/{$location}/document-groups/{$groupId}";
$groupDataFilename = "{$groupDirectory}/group.data";

if (is_file($groupDataFilename))
{
    $data = unserialize(file_get_contents($groupDataFilename));
}

$ajaxURL = COLBY_SITE_URL . '/developer/groups/ajax/update/';
$nameHTML = isset($data->nameHTML) ? $data->nameHTML : '';
$stub = isset($data->stub) ? $data->stub : '';
$descriptionHTML = isset($data->description) ? ColbyConvert::textToHTML($data->description) : '';

?>

<fieldset>
    <progress value="0"
              style="width: 100px; float: right;"></progress>

    <input type="hidden" id="location" value="<?php echo $location; ?>">
    <input type="hidden" id="group-id" value="<?php echo $groupId; ?>">

    <section class="control">
        <header>
            <label for="name">Name</label>
        </header>
        <input type="text"
               id="name"
               value="<?php echo $nameHTML; ?>">
    </section>

    <section class="control" style="margin-top: 10px;">
        <header>
            <label for="description">Metadata</label>
        </header>
        <div style="padding: 4px 10px; font-size: 0.7em;">
            <span class="hash"><?php echo $groupId; ?></span>
            <span style="margin-left: 20px;">location: /<?php echo $location; ?></span>
        </div>
    </section>

    <section class="control" style="margin-top: 10px;">
        <header>
            <label for="description">Description</label>
        </header>
        <textarea id="description"
                  style="height: 300px;"><?php echo $descriptionHTML; ?></textarea>
    </section>

    <section class="control" style="margin-top: 10px;">
        <header>
            <label>Stub</label>
        </header>
        <input type="text"
               id="stub"
               value="<?php echo $stub; ?>">
    </section>

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

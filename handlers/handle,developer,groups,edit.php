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
    $documentGroupId = $_GET['group-id'];
}
else
{
    $documentGroupId = sha1(microtime() . rand());

    header("Location: /developer/groups/edit/?location={$location}&group-id={$documentGroupId}");
}

$groupDirectory = COLBY_SITE_DIRECTORY . "/{$location}/document-groups/{$documentGroupId}";
$groupDataFilename = "{$groupDirectory}/document-group.data";

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

    <h1 style="margin-bottom: 10px;">Document Group Properties Editor</h1>

    <input type="hidden" id="location" value="<?php echo $location; ?>">
    <input type="hidden" id="document-group-id" value="<?php echo $documentGroupId; ?>">

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
        <table id="table1" class="simple-keys-and-values" style="margin: 0px auto; font-size: 0.8em;">
            <tr><th>location</th><td>/<?php echo $location; ?></td></tr>
            <tr><th>document group id</th><td class="hash"><?php echo $documentGroupId; ?></td></tr>
        </table>
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

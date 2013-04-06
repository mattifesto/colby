<?php

$page = new ColbyOutputManager('admin-html-page');

$page->titleHTML = 'Document Type Properties Editor';
$page->descriptionHTML = 'Edit the properties of a document type.';

$page->begin();

if (!ColbyUser::current()->isOneOfThe('Developers'))
{
    include Colby::findSnippet('authenticate.php');

    goto done;
}

$location = $_GET['location'];
$documentGroupId = $_GET['document-group-id'];

if (isset($_GET['document-type-id']))
{
    $documentTypeId = $_GET['document-type-id'];
}
else
{
    $documentTypeId = sha1(microtime() . rand());

    header("Location: /developer/models/edit/" .
           "?location={$location}" .
           "&document-group-id={$documentGroupId}" .
           "&document-type-id={$documentTypeId}");
}

$documentGroupData = unserialize(file_get_contents(
    Colby::findFileForDocumentGroup('document-group.data', $documentGroupId)
    ));


$documentTypeDataFilename = COLBY_SITE_DIRECTORY .
    "/{$location}/" .
    "/document-groups/{$documentGroupId}" .
    "/document-types/{$documentTypeId}" .
    '/document-type.data';

if (is_file($documentTypeDataFilename))
{
    $data = unserialize(file_get_contents($documentTypeDataFilename));
}

$ajaxURL = COLBY_SITE_URL . '/developer/models/ajax/update/';
$nameHTML = isset($data->nameHTML) ? $data->nameHTML : '';
$descriptionHTML = isset($data->description) ? ColbyConvert::textToHTML($data->description) : '';

?>

<fieldset>
    <progress value="0"
              style="width: 100px; float: right;"></progress>

    <h1 style="margin-bottom: 10px;">Document Type Properties Editor</h1>

    <input type="hidden" id="location" value="<?php echo $location; ?>">
    <input type="hidden" id="document-group-id" value="<?php echo $documentGroupId; ?>">
    <input type="hidden" id="document-type-id" value="<?php echo $documentTypeId; ?>">

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
            <label>Metadata</label>
        </header>
        <table id="table1" class="simple-keys-and-values" style="margin: 0px auto; font-size: 0.8em;">
            <tr><th>location</th><td>/<?php echo $location; ?></td></tr>
            <tr><th>document type id</th><td class="hash"><?php echo $documentTypeId; ?></td></tr>
            <tr><th>document group id</th><td class="hash"><?php echo $documentGroupId; ?></td></tr>
            <tr><th>document group name</th><td><?php echo $documentGroupData->nameHTML; ?></td></tr>
        </table>
    </section>

    <section class="control" style="margin-top: 10px;">
        <header>
            <label for="description">Description</label>
        </header>
        <textarea id="description"
                  style="height: 300px;"><?php echo $descriptionHTML; ?></textarea>
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

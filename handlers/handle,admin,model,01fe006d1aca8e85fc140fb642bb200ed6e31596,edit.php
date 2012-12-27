<?php

/**
 * This is the model editor for a model with a title, subtitle, content, and one
 * medium sized image.
 */

$modelId = '01fe006d1aca8e85fc140fb642bb200ed6e31596';
$modelDataFilename = "handle,admin,model,{$modelId}.data";
$modelData = unserialize(file_get_contents(Colby::findHandler($modelDataFilename)));

$page = ColbyOutputManager::beginVerifiedUserPage($modelData->nameHTML,
                                                  $modelData->descriptionHTML,
                                                  'admin');

$archive = ColbyArchive::archiveFromGetData();

// mise en place

$editableContentHTML = ColbyConvert::textToHTML($archive->valueForKey('content'));

?>

<fieldset>

    <?php include(COLBY_SITE_DIRECTORY . '/colby/snippets/editor-common-fields.php'); ?>

    <div><label>Content
        <textarea id="content"
                  style="height: 400px;"><?php echo $editableContentHTML; ?></textarea>
    </label></div>

    <div><label>Image
        <input type="file"
               id="image">
    </label></div>

</fieldset>

<?php

$page->end();

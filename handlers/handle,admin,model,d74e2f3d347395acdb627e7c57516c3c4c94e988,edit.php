<?php

/**
 * This is the model editor for a model with a title, subtitle and content.
 */

$modelId = 'd74e2f3d347395acdb627e7c57516c3c4c94e988';
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

    <div>

</fieldset>

<?php

$page->end();

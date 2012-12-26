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

$ajaxURL = COLBY_SITE_URL . "/admin/model/{$modelId}/ajax/update/";

$customPageStubTextHTML = $archive->valueForKey('customPageStubTextHTML');
$stubIsLockedChecked = $archive->valueForKey('stubIsLocked') ? ' checked="checked"' : '';

$editableContentHTML = ColbyConvert::textToHTML($archive->valueForKey('content'));

$isPublished = $archive->valueForKey('isPublished') ? ' checked="checked"' : '';
$currentUserId = ColbyUser::currentUserId();
$javascriptPublicationDate = $archive->valueForKey('publicationDate') * 1000;

?>

<fieldset>
    <input type="hidden" id="archive-id" value="<?php echo $archive->archiveId(); ?>">
    <input type="hidden" id="view-id" value="<?php echo $archive->valueForKey('viewId'); ?>">
    <input type="hidden" id="preferred-page-stub" value="<?php echo $archive->valueForKey('preferredPageStub'); ?>">
    <input type="hidden" id="published-by" value="<?php echo $archive->valueForKey('publishedBy'); ?>">
    <input type="hidden" id="publication-date" value="<?php echo $archive->valueForKey('publicationDate'); ?>">

    <progress value="0"
              style="width: 100px; float: right;"></progress>

    <div><label>Title
        <input type="text"
               id="title"
               value="<?php echo $archive->valueForKey('titleHTML'); ?>">
    </label></div>

    <div style="padding: 0px 50px; font-size: 0.75em;">
        <style scoped="scoped">
            .stub
            {
                font-family: "Courier New", monospace;
            }

            table.stubs
            {
                width: 100%;
                margin-bottom: 5px;
            }

            table.stubs tr td:first-child
            {
                width: 100px;
                text-align: right;
            }
        </style>
        <table class="stubs"><tr>
            <td>Preferred URL:</td>
            <td id="preferred-stub-view" class="stub"><?php echo $archive->model->preferredStub(); ?></td>
        </tr><tr>
            <td>Actual URL:</td>
            <td id="stub-view" class="stub"><?php echo $archive->model->stub(); ?></td>
        </td></table>
        <label style="float:right; margin-left: 20px;">
            <input type="checkbox"
                   id="stub-is-locked"
                   <?php echo $stubIsLockedChecked; ?>> Lock Stub
        </label>
        <label>Custom Stub Text
            <input type="text"
                   id="custom-page-stub-text"
                   value="<?php echo $customPageStubTextHTML; ?>">
        </label>
    </div>

    <div><label>Subtitle
        <input type="text"
               id="subtitle"
               value="<?php echo $archive->valueForKey('subtitleHTML'); ?>">
    </label></div>

    <div><label>Content
        <textarea id="content"
                  style="height: 400px;"><?php echo $editableContentHTML; ?></textarea>
    </label></div>

    <div>
        <label style="float: right;">
            <input type="checkbox"
                   id="is-published"
                   <?php echo $isPublished; ?>
                   onchange="handleIsPublishedChanged(this);">
        Published</label>
        <label>Publication Date:
            <input type="text"
                   id="publication-date-text"
                   class="ignore"
                   onblur="handlePublicationDateBlurred(this);">
        </label>
    </div>

</fieldset>

<div id="error-log"></div>

<script>
"use strict";

var ajaxURL = '<?php echo $ajaxURL; ?>'
var currentUserId = <?php echo $currentUserId; ?>;
var groupStub = '<?php echo $archive->valueForKey('groupStub'); ?>';
var publicationDate = <?php echo $javascriptPublicationDate; ?>;

</script>

<script src="<?php echo COLBY_SITE_URL . '/colby/javascript/ColbyPageEditor.js'; ?>"></script>

<?php

$page->end();

<?php

/**
 * Variables that need to be set:
 *
 * $archive ColbyArchive
 * $modelId string
 */

$viewId = $archive->valueForKey('viewId');
$archiveId = $archive->archiveId();

$previewURL = COLBY_SITE_URL . "/admin/view/{$viewId}/?action=preview&archive-id={$archiveId}";

?>

<input type="hidden" id="archive-id"
       value="<?php echo $archive->archiveId(); ?>">
<input type="hidden" id="view-id"
       value="<?php echo $viewId; ?>">
<input type="hidden" id="preferred-page-stub"
       value="<?php echo $archive->valueForKey('preferredPageStub'); ?>">
<input type="hidden" id="published-by"
       value="<?php echo $archive->valueForKey('publishedBy'); ?>">
<input type="hidden" id="publication-date"
        value="<?php echo $archive->valueForKey('publicationDate'); ?>">

<div style="position: absolute; top: 40px; right: 10px; text-align: right;">
    <div><progress value="0" style="width: 100px; margin-bottom: 5px;"></progress></div>
    <div style="font-size: 0.7em;">
        <div>
            Last modified<br/>
            <span id="modified" class="time"
                  data-timestamp="<?php echo $archive->modified() * 1000; ?>">
            </span>
        </div>
        <div><a href="<?php echo $previewURL; ?>">preview</a></div>
    </div>
</div>

<section class="control">
    <header><label for="title">Title</label></header>
    <input type="text" id="title"
           value="<?php echo $archive->valueForKey('titleHTML'); ?>">
</section>

<section class="control"
         style="margin-top: 10px;">
    <header><label for="subtitle">Subtitle</label></header>
    <input type="text" id="subtitle"
           value="<?php echo $archive->valueForKey('subtitleHTML'); ?>">
</section>

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
        <input type="checkbox" id="stub-is-locked"
               <?php if ($archive->valueForKey('stubIsLocked')) echo 'checked'; ?>>
        Locked
    </label>

    <label>
        Custom Stub Text
        <input type="text" id="custom-page-stub-text"
               value="<?php echo $archive->valueForKey('customPageStubTextHTML'); ?>">
    </label>
</div>

<section class="control"
         style="margin-top: 10px;">
    <header>
        <label for="publication-date-text">Publication Date</label>
        <label style="float: right;">
            <input type="checkbox" id="is-published"
                   <?php if ($archive->valueForKey('isPublished')) echo 'checked'; ?>
                   onchange="ColbyPageEditor.handleIsPublishedChanged(this);">
            Published
        </label>
    </header>
    <input type="text" id="publication-date-text" class="ignore"
           onblur="ColbyPageEditor.handlePublicationDateBlurred(this);">
</section>

<script>
"use strict";

var ajaxURL = '<?php echo COLBY_SITE_URL . "/admin/model/{$modelId}/ajax/update/"; ?>'
var currentUserId = <?php echo ColbyUser::currentUserId(); ?>;
var groupStub = '<?php echo $archive->valueForKey('groupStub'); ?>';
var publicationDate = <?php echo $archive->valueForKey('publicationDate') * 1000; ?>;
</script>

<script src="<?php echo COLBY_SITE_URL . '/colby/javascript/ColbyPageEditor.js'; ?>"></script>

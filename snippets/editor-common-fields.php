<?php

/**
 * Variables that need to be set:
 *
 * $archive         ColbyArchive
 * $archiveId       string
 * $documentGroupId string
 * $documentTypeId  string
 */

$previewURL = COLBY_SITE_URL . "/admin/document/preview/?archive-id={$archiveId}";

$publishedTimeStamp = $archive->valueForKey('publishedTimeStamp');

if (!$publishedTimeStamp)
{
    /**
     * TODO: Remove this when we are sure that all documents are using
     * `publishedTimeStamp` instead of `publicationDate`.
     */

    $publishedTimeStamp = $archive->valueForKey('publicationDate');
}

?>

<input type="hidden" id="archive-id"
       value="<?php echo $archiveId; ?>">
<input type="hidden" id="document-group-id"
       value="<?php echo $documentGroupId; ?>">
<input type="hidden" id="document-type-id"
       value="<?php echo $documentTypeId; ?>">
<input type="hidden" id="published-by"
       value="<?php echo $archive->valueForKey('publishedBy'); ?>">
<input type="hidden" id="published-time-stamp"
       value="<?php echo $publishedTimeStamp; ?>">
<input type="hidden" id="uri-is-custom"
       value="<?php echo $archive->valueForKey('uriIsCustom'); ?>">

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
    <header><label for="subtitle">URI</label></header>
    <input type="text" id="uri"
           value="<?php echo $archive->valueForKey('uri'); ?>"
           style="padding-top: 9px; padding-bottom: 9px; font-family: 'Courier New'; font-size: 0.7em;">
</section>

<section class="control"
         style="margin-top: 10px;">
    <header><label for="subtitle">Subtitle</label></header>
    <input type="text" id="subtitle"
           value="<?php echo $archive->valueForKey('subtitleHTML'); ?>">
</section>

<section class="control"
         style="margin-top: 10px;">
    <header>
        <label for="publication-date-text">Publication Date</label>
        <label style="float: right;">
            <input type="checkbox" id="is-published"
                   <?php if ($archive->valueForKey('isPublished')) echo 'checked'; ?>>
            Published
        </label>
    </header>
    <input type="text" id="publication-date-text" class="ignore">
</section>

<script>
"use strict";

var ajaxURL = '<?php echo COLBY_SITE_URL . "/admin/document/update/"; ?>'
var currentUserId = <?php echo ColbyUser::currentUserId(); ?>;
var groupStub = '<?php echo $archive->valueForKey('groupStub'); ?>';
</script>

<script src="<?php echo COLBY_SITE_URL . '/colby/javascript/ColbyPageEditor.js'; ?>"></script>

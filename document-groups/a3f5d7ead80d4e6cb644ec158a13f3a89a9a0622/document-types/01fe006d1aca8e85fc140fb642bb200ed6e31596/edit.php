<?php // Document editor for a document with image

$documentGroupId = 'a3f5d7ead80d4e6cb644ec158a13f3a89a9a0622';
$documentTypeId = '01fe006d1aca8e85fc140fb642bb200ed6e31596';
$archiveId = $_GET['archive-id'];

$documentTypeData = unserialize(file_get_contents(
    Colby::findFileForDocumentType('document-type.data', $documentGroupId, $documentTypeId)));

$page = new ColbyOutputManager('admin-html-page');

$page->titleHTML = $documentTypeData->nameHTML;
$page->descriptionHTML = $documentTypeData->descriptionHTML;

$page->begin();

if (!ColbyUser::current()->isOneOfThe('Administrators'))
{
    include Colby::findSnippet('authenticate.php');

    goto done;
}

$archive = ColbyArchive::open($archiveId);

?>

<fieldset>

    <?php include(COLBY_SITE_DIRECTORY . '/colby/snippets/editor-common-fields.php'); ?>

    <section class="control"
             style="margin-top: 10px;">
        <header><label for="content">Content</label></header>
        <textarea id="content" style="height: 400px;"><?php
            echo ColbyConvert::textToHTML($archive->valueForKey('content'));
        ?></textarea>
    </section>

    <div style="overflow: hidden; margin-top: 10px;">
        <div style="float: left;">
            <img id="image-thumbnail"
                 style="float: left; width: 150px; margin-right: 10px;"
                 src="<?php if ($filename = $archive->valueForKey('imageFilename')) echo $archive->url($filename); ?>">
        </div>
        <div style="overflow: hidden;">
            <section class="control">
                <header><label for="image-file">Image File</label></header>
                <input type="file" id="image-file">
            </section>

            <section class="control"
                     style="margin-top: 10px;">
                <header><label for="image-caption">Image Caption</label></header>
                <textarea id="image-caption"
                          style="height: 100px;"><?php
                    echo $archive->valueForKey('imageCaptionHTML');
                ?></textarea>
            </section>

            <section class="control"
                     style="margin-top: 10px;">
                <header><label for="image-alternative-text">Image Alternative Text</label></header>
                <textarea id="image-alternative-text"
                          style="height: 100px;"><?php
                    echo $archive->valueForKey('imageAlternativeTextHTML');
                ?></textarea>
            </section>
        </div>
    </div>

</fieldset>

<script>
"use strict";

function updateComplete(event)
{
    if ('imageURL' in event.detail)
    {
        var img = document.getElementById('image-thumbnail');

        img.src = event.detail.imageURL;
    }
}

document.addEventListener('ColbyPageUpdateComplete', updateComplete, false);

</script>

<?php

done:

$page->end();

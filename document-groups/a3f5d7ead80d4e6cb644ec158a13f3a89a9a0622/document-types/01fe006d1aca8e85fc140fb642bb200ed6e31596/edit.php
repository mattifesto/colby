<?php // Edit COLBY_PAGES_DOCUMENT_GROUP_ID -> COLBY_PAGE_DOCUMENT_TYPE_ID

if (!ColbyUser::current()->isOneOfThe('Administrators'))
{
    return include CBSystemDirectory . '/handlers/handle-authorization-failed.php';
}

$documentTypeFilename   = Colby::findFileForDocumentType('document-type.data',
                                                         COLBY_PAGES_DOCUMENT_GROUP_ID,
                                                         COLBY_PAGE_DOCUMENT_TYPE_ID);
$documentTypeData       = unserialize(file_get_contents($documentTypeFilename));

CBHTMLOutput::begin();
CBHTMLOutput::setTitleHTML($documentTypeData->nameHTML);
CBHTMLOutput::setDescriptionHTML($documentTypeData->descriptionHTML);

include CBSystemDirectory . '/sections/admin-page-settings.php';

CBHTMLOutput::addCSSURL(CBSystemURL . '/css/admin.css');
CBHTMLOutput::addJavaScriptURL(CBSystemURL . '/javascript/ColbyFormManager.js');

$spec                           = new stdClass();
$spec->selectedMenuItemName     = 'pages';
$spec->selectedSubmenuItemName  = 'old-style';

CBAdminPageMenuView::renderModelAsHTML(CBAdminPageMenuView::specToModel($spec));

$archiveId  = $_GET['archive-id'];
$archive    = ColbyArchive::open($archiveId);

?>

<main style="width: 720px; margin: 50px auto;">
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
            <div style="min-width: 150px; min-height: 150px; margin-right: 10px; float: left; background-color: #efefef;">
                <img id="image-thumbnail"
                     style="display: block; width: 150px;"
                     src="<?php if ($filename = $archive->valueForKey('documentImageBasename')) echo $archive->dataURL(), '/', $filename; ?>">
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
</main>

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

CBAdminPageFooterView::renderModelAsHTML();

CBHTMLOutput::render();

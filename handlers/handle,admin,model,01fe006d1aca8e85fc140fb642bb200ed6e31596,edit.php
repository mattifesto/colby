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

?>

<fieldset>

    <?php include(COLBY_SITE_DIRECTORY . '/colby/snippets/editor-common-fields.php'); ?>

    <section>
        <header><label for="content">Content</label></header>
        <textarea id="content" style="height: 400px;"><?php
            echo ColbyConvert::textToHTML($archive->valueForKey('content'));
        ?></textarea>
    </section>

    <section>
        <header>Image</header>
        <div style="overflow: hidden;">
            <img id="imageThumbnail"
                 style="float: left; width: 150px; margin-right: 10px;"
                 src="<?php if ($filename = $archive->valueForKey('imageFilename')) echo $archive->url($filename); ?>">
            <label>Image File
                <input type="file"
                       id="image">
            </label>
        </div>
    </section>

</fieldset>

<script>
"use strict";

function updateComplete(event)
{
    if ('imageURL' in event.detail)
    {
        var img = document.getElementById('imageThumbnail');

        img.src = event.detail.imageURL;
    }
}

document.addEventListener('ColbyPageUpdateComplete', updateComplete, false);

</script>
<?php

$page->end();

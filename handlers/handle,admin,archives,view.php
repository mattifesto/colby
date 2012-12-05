<?php

$page = ColbyOutputManager::beginVerifiedUserPage('Archive Details', 'Viewmv  the contents of an archive.', 'admin');

$archiveId = $_GET['archive-id'];

$archive = ColbyArchive::open($archiveId);

$attributesHTML = ColbyConvert::textToHTML(var_export($archive->attributes(), true));
$rootObjectHTML = ColbyConvert::textToHTML(var_export($archive->rootObject(), true));

?>

<style>
    pre
    {
        margin-bottom: 50px;
    }

    h6
    {
        margin-bottom: 20px;
    }
</style>

<h6>Attributes</h6>
<pre><?php echo $attributesHTML; ?></pre>

<h6>Root Object</h6>
<pre><?php echo $rootObjectHTML; ?></pre>

<?php

$page->end();

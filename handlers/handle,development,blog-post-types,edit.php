<?php

$page = ColbyOutputManager::beginVerifiedUserPage('Post Type Editor',
                                                  'Edit the attributes of a blog post type.',
                                                  'admin');

if (isset($_GET['blog-post-type-id']))
{
    $blogPostTypeId = $_GET['blog-post-type-id'];
}
else
{
    $blogPostTypeId = sha1(microtime() . rand());

    header("Location: {$_SERVER['REQUEST_URI']}?blog-post-type-id={$blogPostTypeId}");
}

$dataFilename = "handle,admin,blog,{$blogPostTypeId}.data";

$absoluteDataFilename = Colby::findHandler($dataFilename);

if ($absoluteDataFilename)
{
    $data = unserialize(file_get_contents($absoluteDataFilename));
}

$ajaxURL = COLBY_SITE_URL . '/development/blog-post-types/ajax/update/';
$nameHTML = isset($data->nameHTML) ? $data->nameHTML : '';
$descriptionHTML = isset($data->description) ? ColbyConvert::textToHTML($data->description) : '';

?>

<fieldset>
    <progress value="0"
              style="width: 100px; float: right;"></progress>

    <div><label>Blog Post Type Id
        <input type="text"
               id="blog-post-type-id"
               value="<?php echo $blogPostTypeId; ?>"
               readonly="readonly"
               style="font-family: monospace;">
    </label></div>

    <div><label>Name
        <input type="text"
               id="name"
               value="<?php echo $nameHTML; ?>">
    </label></div>

    <div><label>Description
        <textarea id="description"
                  style="height: 300px;"><?php echo $descriptionHTML; ?></textarea>
    </label></div>

    <div id="error-log"></div>
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

$page->end();

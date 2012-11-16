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

$dataFilename = COLBY_SITE_DIRECTORY . "/colby/handlers/handle,admin,blog,{$blogPostTypeId}.data";

if (file_exists($dataFilename))
{
    $data = unserialize(file_get_contents($dataFilename));
}

$title = isset($data->titleHTML) ? $data->titleHTML : '';
$description = isset($data->descriptionHTML) ? $data->descriptionHTML : '';

?>

<fieldset>
    <div><label>Blog Post Type Id
        <input type="text" 
               value="<?php echo $blogPostTypeId; ?>"
               readonly="readonly" 
               style="font-family: monospace;">
    </label></div>
    
    <div><label>Title
        <input type="text"
               id="title"
               class="form-field"
               value="<?php echo $title; ?>"
               onkeydown="handleValueChanged(this);">
    </label></div>

    <div><label>Description
        <textarea id="description"
                  class="form-field"
                  style="height: 400px;"
                  onkeydown="handleValueChanged(this);"><?php echo $description; ?></textarea>
    </label></div>
</fieldset>
    
<?php

$page->end();
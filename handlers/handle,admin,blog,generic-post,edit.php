<?php

$stubs = ColbyRequest::decodedStubs();

if (count($stubs) === 1)
{
    $fileId = sha1(microtime() . rand() . ColbyUser::currentUserId());

    header('Location: /edit-generic-blog-post/' . $fileId . '/');
}
else
{
    $fileId = $stubs[1];
}

$args = new stdClass();
$args->title = 'Generic Blog Post Editor';
$args->description = 'Create and edit generic blog posts.';

ColbyPage::beginAdmin($args);

?>

<style>
input,
textarea
{
    display: block;
    width: 100%;
    padding: 4px;
    margin-top: 5px;
    border: 1px solid LightGray;
}

fieldset
{
    width: 600px;
    border: none;
}

fieldset > div + div
{
    margin-top: 10px;
}
</style>

<h1>Generic Blog Post Editor</h1>

<p>fileId: <?php echo $fileId; ?>

<fieldset>
    <div>Title <input type="text" class="form-field" onkeypress="handleKeyPressed(this);"></div>
    <div>Content <textarea class="form-field" style="height: 400px;" onkeypress="handleKeyPressed(this);"></textarea></div>
    <div><input type="date"></div>
</fieldset>

<script>
"use strict";

var needsUpdate = false;
var isUpdating = false;
var timer = null;

function handleKeyPressed(sender)
{
    if (sender)
    {
        sender.style.backgroundColor = 'LightYellow';
    }

    needsUpdate = true;

    if (!isUpdating)
    {
        if (timer)
        {
            clearTimeout(timer);
        }

        timer = setTimeout(updateBlogPost, 2000);
    }
}

function updateBlogPost()
{
    isUpdating = true;
    needsUpdate = false;

    setTimeout(handleBlogPostUpdated, 3000); // mimic ajax call
}

function handleBlogPostUpdated()
{
    isUpdating = false;

    if (needsUpdate)
    {
        handleKeyPressed(null);
    }
    else
    {
        var elements = document.getElementsByClassName('form-field');

        for (var i = 0; i < elements.length; i++)
        {
            elements[i].style.backgroundColor = 'Transparent';
        }
    }
}
</script>

<?php

ColbyPage::end();

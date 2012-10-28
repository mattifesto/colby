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
    <div>Title <input type="text" onkeypress="handleKeyPressed(this);"></div>
    <div>Content <textarea style="height: 400px;" onkeypress="handleKeyPressed(this);"></textarea></div>
    <div><input type="date"></div>
</fieldset>

<script>
"use strict";

function handleKeyPressed(sender)
{
    sender.style.backgroundColor = 'LightYellow';
}
</script>

<?php

ColbyPage::end();

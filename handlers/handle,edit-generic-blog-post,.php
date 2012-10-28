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

<h1>Generic Blog Post Editor</h1>

<p>fileId: <?php echo $fileId; ?>

<?php

ColbyPage::end();

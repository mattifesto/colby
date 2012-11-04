<?php

if (!isset($archive))
{
    ColbyPage::requireVerifiedUser();

    if (!isset($_GET['archive-id']))
    {
        return false;
    }

    $archive = ColbyArchive::open($_GET['archive-id']);
}

// TODO: Get a better description.

$args = new stdClass();
$args->title = $archive->rootObject()->title;
$args->description = 'A Blog Post';

ColbyPage::begin($args);

?>

<h1><?php echo $archive->rootObject()->titleHTML; ?></h1>

<div class="formatted-content"><?php echo $archive->rootObject()->contentHTML; ?></div>

<?php

ColbyPage::end();

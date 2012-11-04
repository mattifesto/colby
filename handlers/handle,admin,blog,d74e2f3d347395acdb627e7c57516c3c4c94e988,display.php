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

$args = new stdClass();
$args->title = $archive->rootObject()->title;
$args->description = 'This is the front page.';

// setting the header and footer is only required to override the default
// which is COLBY_SITE_DIRECTORY . '/snippets/(header|footer).php'

$args->header = COLBY_SITE_DIRECTORY . '/colby/snippets/header.php';
$args->footer = COLBY_SITE_DIRECTORY . '/colby/snippets/footer.php';

ColbyPage::begin($args);

?>

<h1><?php echo $archive->rootObject()->titleHTML; ?></h1>

<div class="formatted-content"><?php echo $archive->rootObject()->contentHTML; ?></div>

<?php

ColbyPage::end();

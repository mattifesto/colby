<?php

$args = new stdClass();
$args->title = 'Log In';
$args->description = 'You must log in to view this page.';

// setting the header and footer is only required to override the default
// which is COLBY_SITE_DIRECTORY . '/snippets/(header|footer).php'

$args->header = COLBY_SITE_DIRECTORY . '/colby/snippets/header.php';
$args->footer = COLBY_SITE_DIRECTORY . '/colby/snippets/footer.php';

ColbyPage::begin($args);

?>

<h1><?php echo $args->title; ?></h1>

<p><?php echo $args->description; ?>

<p><a href="<?php echo ColbyUser::loginURL(); ?>">log in</a>

<?php

ColbyPage::end();

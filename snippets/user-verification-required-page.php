<?php

$args = new stdClass();
$args->title = 'Verification Required';
$args->description = 'You must be verified by an adminstrator to view this page.';

// setting the header and footer is only required to override the default
// which is COLBY_SITE_DIRECTORY . '/snippets/(header|footer).php'

$args->header = COLBY_SITE_DIRECTORY . '/colby/snippets/header.php';
$args->footer = COLBY_SITE_DIRECTORY . '/colby/snippets/footer.php';

ColbyPage::begin($args);

?>

<p style="text-align: right;">
    <a href="<?php echo ColbyUser::logoutURL(); ?>">log out</a>

<h1><?php echo $args->title; ?></h1>

<p><?php echo $args->description; ?>

<?php

ColbyPage::end();

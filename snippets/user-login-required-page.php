<?php

$args = new stdClass();
$args->title = 'Log In Required';
$args->description = 'You must log in to view this page.';

ColbyPage::beginAdmin($args);

?>

<p><?php echo $args->description; ?>

<p><?php echo ColbyUser::loginHyperlink(); ?>

<?php

ColbyPage::end();

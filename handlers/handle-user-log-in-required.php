<?php

$page = ColbyOutputManager::beginPage('Log In Required', 'You must log in to view this page.');

?>

<p>You must log in to view this page.

<p><?php echo ColbyUser::loginHyperlink(); ?>

<?php

$page->end();

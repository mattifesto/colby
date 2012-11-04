<?php

$args = new stdClass();
$args->title = 'Front Page';
$args->description = 'This is the front page.';

ColbyPage::begin($args);

?>

<h1><?php echo $args->title; ?></h1>

<p><?php echo $args->description; ?>

<?php

ColbyPage::end();

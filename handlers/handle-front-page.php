<?php

$page = new ColbyOutputManager();

$page->titleHTML = 'Front Page';
$page->descriptionHTML = 'This is the front page.';

$page->begin();

?>

<p>This is the front page.

<?php

$page->end();

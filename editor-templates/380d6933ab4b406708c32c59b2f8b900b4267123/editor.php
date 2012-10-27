<?php

$args = new stdClass();
$args->title = 'Colby Configuration';
$args->description = 'Use this page to configure Colby.';

ColbyPage::beginAdmin($args);

?>

<h1>Generic Blog Post Editor</h1>

<?php

ColbyPage::end();

<?php

header($_SERVER['SERVER_PROTOCOL'] . ' 404 Not Found');

$args = new stdClass();
$args->title = 'Page Not Found';
$args->description = 'The page you requested was not found.';

ColbyPage::begin($args);

?>

<h1><?php echo $args->title; ?></h1>

<p><?php echo $args->description; ?>

<p>
    <code><?php

        // put spaces around the following three characters: / ? &
        // to allow for nice line break behavior

        echo COLBY_SITE_URL,
            htmlspecialchars(
                preg_replace('/([\/\?&])/',
                             ' $1 ',
                             $_SERVER['REQUEST_URI']));


    ?></code>

<?php

ColbyPage::end();

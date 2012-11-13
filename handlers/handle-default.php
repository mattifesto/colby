<?php

header($_SERVER['SERVER_PROTOCOL'] . ' 404 Not Found');

$page = ColbyOutputManager::beginPage('Page Not Found', 'The page you requested was not found.');

?>

<p>The page you requested was not found.

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

$page->end();

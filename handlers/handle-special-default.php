<?php

header($_SERVER['SERVER_PROTOCOL'] . ' 404 Not Found');

$args = new stdClass();
$args->title = 'Colby URL Not Handled';
$args->description = 'The URL requested was not recognized.';

// setting the header and footer is only required to override the default
// which is COLBY_SITE_DIRECTORY . '/snippets/(header|footer).php'

$args->header = COLBY_SITE_DIRECTORY . '/colby/snippets/header.php';
$args->footer = COLBY_SITE_DIRECTORY . '/colby/snippets/footer.php';

ColbyPage::begin($args);

?>

<h1>Colby URL Not Handled</h1>

<blockquote><code><?php

    // put spaces around the following three characters: / ? &
    // to allow for nice line break behavior

    echo COLBY_SITE_URL,
        htmlspecialchars(
            preg_replace('/([\/\?&])/',
                         ' $1 ',
                         $_SERVER['REQUEST_URI']));


?></code></blockquote>

<p>You are seeing this page because a handler was not provided for this URL. Furthermore, a default handler has not been provided for when a handler isn't found. Create a file named:

<blockquote><code><?php

    echo COLBY_SITE_DIRECTORY .
        '/handlers/handle-special-default-handler.php';

?></code></blockquote>

<p>which can either generate a 404 error or do more complex processing of the requested URL to generate pages.

<p>This page generates a 404 error.

<?php

ColbyPage::end();

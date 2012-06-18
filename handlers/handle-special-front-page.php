<?php

header($_SERVER['SERVER_PROTOCOL'] . ' 404 Not Found');

$args = new stdClass();
$args->title = 'Default Colby Website Front Page';
$args->description = 'This is the default front page for a fresh Colby installation.';

// setting the header and footer is only required to override the default
// which is COLBY_SITE_DIRECTORY . '/snippets/(header|footer).php'

$args->header = COLBY_SITE_DIRECTORY . '/colby/snippets/header.php';
$args->footer = COLBY_SITE_DIRECTORY . '/colby/snippets/footer.php';

ColbyPage::begin($args);

?>

<h1 style="text-align: center;">Welcome to Colby</h1>

<p>You are seeing this default front page because you have not provided a handler for the front page. Create a file named:

<blockquote><code><?php

    echo COLBY_SITE_DIRECTORY .
        '/handlers/handle-special-front-page.php';

?></code></blockquote>

<p>which should contain code to generate the front page for your site.

<?php

ColbyPage::end();

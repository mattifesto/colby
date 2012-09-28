<?php

define('COLBY_SITE_DIR', $_SERVER['DOCUMENT_ROOT']);

$indexFilename = COLBY_SITE_DIR . '/index.php';
$configurationFilename = COLBY_SITE_DIR . '/colby-configuration.php';
$htaccessFilename = COLBY_SITE_DIR . '/.htaccess';

?>


<h1>Colby Setup</h1>

<?php

if (   is_file($configurationFilename)
    || is_file($htaccessFilename)
    || is_file($indexFilename))
{
    // if .htaccess exists correctly in the web root directory
    // the user will not even be able to load this file
    // so if any of the above files exist
    // it means that the install is only partially complete
    // and that shouldn't really ever even happen so just show a message

    ?>

    <!doctype html>
    <html lang="en">
        <head>
            <meta charset="UTF-8">
            <title>Colby Setup</title>
            <meta name="description" content="This page is meant to bootstrap a fresh install of a Colby based website.">
        </head>
        <body>
            <h1>Colby Setup</h1>
            <p>This site has been partially setup and is in an unknown state.
        </body>
    </html>

    <?php

    exit;
}

copy(__DIR__ . '/colby-configuration-bootstrap.php', $configurationFilename);
copy(__DIR__ . '/htaccess.template', $htaccessFilename);
copy(__DIR__ . '/index.template', $indexFilename);

header('Location: /colby/configuration/');

?>



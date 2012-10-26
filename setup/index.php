<?php

define('COLBY_SITE_DIR', $_SERVER['DOCUMENT_ROOT']);

$dataDirectory = COLBY_SITE_DIR . '/data';

$configurationFilename = COLBY_SITE_DIR . '/colby-configuration.php';
$gitignoreFilename = COLBY_SITE_DIR . '/.gitignore';
$htaccessFilename = COLBY_SITE_DIR . '/.htaccess';
$indexFilename = COLBY_SITE_DIR . '/index.php';

?>


<h1>Colby Setup</h1>

<?php

if (   file_exists($dataDirectory)
    || file_exists($configurationFilename)
    || file_exists($gitignoreFilename)
    || file_exists($htaccessFilename)
    || file_exists($indexFilename))
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

mkdir($dataDirectory);
copy(__DIR__ . '/colby-configuration-bootstrap.php', $configurationFilename);
copy(__DIR__ . '/gitignore.template', $gitignoreFilename);
copy(__DIR__ . '/htaccess.template', $htaccessFilename);
copy(__DIR__ . '/index.template', $indexFilename);

header('Location: /colby/configuration/');

?>



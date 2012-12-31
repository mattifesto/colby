<?php

define('COLBY_SITE_DIR', $_SERVER['DOCUMENT_ROOT']);

$dataDirectory = COLBY_SITE_DIR . '/data';

$configurationFilename = COLBY_SITE_DIR . '/colby-configuration.php';
$gitignoreFilename = COLBY_SITE_DIR . '/.gitignore';
$htaccessFilename = COLBY_SITE_DIR . '/.htaccess';
$indexFilename = COLBY_SITE_DIR . '/index.php';

$shouldPerformInstallation = (isset($_GET['install']) && $_GET['install'] == 'true');

if (!$shouldPerformInstallation)
{
    ?>

    <!doctype html>
    <html lang="en">
        <head>
            <meta charset="UTF-8">
            <title>Template HTML5 Document</title>
            <meta name="description"
                  content="This HTML of this page represents a template for all HTML pages.">
            <link rel="stylesheet"
                  type="text/css"
                  href="http://fonts.googleapis.com/css?family=Source+Sans+Pro:400,700">
            <style>
                *
                {
                    font-family: 'Source Sans Pro', 'Helvetica Neue', Arial, sans-serif;
                }

                body
                {
                    width: 600px;
                    margin: 0px auto 100px;
                }

                dt
                {
                    margin-bottom: 0.5em;
                    font-weight: bold;
                }

                dd
                {
                    margin-bottom: 1.0em;
                    margin-left: 2.0em;
                }
            </style>
        </head>
        <body>
            <h1>Welcome to Colby</h1>

            <p>This page will allow you perform the initial site installation. If you choose to continue some template files will be placed in your sites root directory.

            <dl>
                <dt>.htaccess
                <dd>This file will redirect all requests to the colby system and disallow direct access to certain protected files like Git repostiory files, individual PHP files, and data files.
                <dt>.gitignore
                <dd>This file will configure git to ignore certain files and directories that will be created when using a Colby site but that shouldn't be checked in. One example is all of the files in the data directory.
                <dt>colby-configuration.php
                <dd>The setup process will create this file which you will need to edit to provide site metadata and database connection information. You will be reminded of this after this initial setup process is complete.
                <dt>data directory
                <dd>A directory named 'data' will be created in the website root. This directory will be used to hold the archives. There will be usually one archive per unit of data. A unit of data may be a page, blog post, product, user, but is almost always associated with something that has its own URL and web page.
                <dt>index.php
                <dd>All pages on the site will be directed to this file which will send the URLs through the Colby system to generate content. The reason the file is created in your website root is that you may want to customize this file to handle some or all of the URLs in a different way.
            </dl>

            <p>After you complete the initial installation there are a few more required steps such as editing the colby-configuration.php file mentioned above and installing the database. You will be guided to complete these steps. After clicking on the following link you will be redirected and to another page and this page will no longer be needed or available.

            <p style="text-align: center;"><a href="/colby/setup/?install=true">perform initial installation now</a>
        </body>
    </html>

    <?php

    exit;
}

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

header('Location: /developer/configuration/');

?>


<!doctype html>
<html lang="en">
    <head>
        <meta charset="UTF-8">
        <title>Template HTML5 Document</title>
        <meta name="description"
              content="This HTML of this page represents a template for all HTML pages.">
        <link rel="stylesheet"
              type="text/css"
              href="https://fonts.googleapis.com/css?family=Source+Sans+Pro:400,700">
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
            <dd>This file will configure Git to ignore certain files and directories that will be created when using a Colby site but that shouldn't be checked in. One example is all of the files in the data directory.

            <dt>colby-configuration.php
            <dd>The setup process will create this file, which you will need to edit, to provide site metadata and database connection information. This file will be ignored by Git and needs to be manually created for each instance of your site. Site instances, such as development, test, and production will each probably have different values for the constants set in this file. You will be reminded to edit the file after this initial setup process is complete.

            <dt>data directory
            <dd>A directory named 'data' will be created in the website root. This directory will be used to hold the archives. There will be usually one archive per unit of data. A unit of data may be a page, blog post, product, user, but is almost always associated with something that has its own URL and web page.

            <dt>favicon.gif
            <dt>favicon.ico
            <dd>Zero length files will be created with these names because the files are often requested by browsers and it is faster to have zero length files available than to run a full Colby request just to generate a 404 error. When these files are zero length browsers treat them as if they didn't exist at all, so it's still effectively a 404, only faster.

            <dt>index.php
            <dd>Any URL that doesn't refer to an actual file or references a file for which direct access is not allowed, such as &ldquo;.php&rdquo; files, will be redirected to this file which will send the URLs through the Colby system to generate content.

            <dt>site-configuration.php
            <dd>The setup process will create this file which may be edited to provide configuration settings and perform actions that are shared between all instances of your site. If you need to load libraries, this is the place to do it.

            <dt>version.php
            <dd>This file contains the version number for the website. It should be incremented by one and checked in for each release. The version number should always be a whole number in the same way the Firefox and Chrome now use only whole number versions that will potentially go quite high.
        </dl>

        <p>After you complete the initial installation there are a few more required steps such as editing the colby-configuration.php file mentioned above and installing the database. You will be guided to complete these steps. After clicking on the following link you will be redirected and to another page and this page will no longer be needed or available.

        <p style="text-align: center;"><a href="/colby/setup/?install=true">perform initial installation now</a>
    </body>
</html>

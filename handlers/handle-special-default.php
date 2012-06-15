<?php

header($_SERVER['SERVER_PROTOCOL'] . ' 404 Not Found');

?>
<!DOCTYPE html>
<html lang="en">
    <head>
        <meta charset="utf-8">
        <title>Default Colby Website Handler Not Found</title>
        <link rel="stylesheet"
              href="<?php echo COLBY_SITE_URL;
                    ?>/colby/css/equalize.css">
        <link rel="stylesheet"
              href="<?php echo COLBY_SITE_URL;
                    ?>/colby/css/style.css">
    </head>
    <body>

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

    </body>
</html>

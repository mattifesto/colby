<!DOCTYPE html>
<html lang="en">
    <head>
        <meta charset="utf-8">
        <title>Default Colby Website Front Page</title>
        <link rel="stylesheet" 
              href="<?php echo COLBY_SITE_URL;
                    ?>/colby/css/equalize.css">
        <link rel="stylesheet" 
              href="<?php echo COLBY_SITE_URL;
                    ?>/colby/css/style.css">
    </head>
    <body>
    
        <h1 style="text-align: center;">Welcome to Colby</h1>
        
        <p>You are seeing this default front page because you have not provided a handler for the front page. Create a file named:
        
        <blockquote><code><?php
        
            echo COLBY_SITE_DIRECTORY .
                '/handlers/handle-special-front-page.php';
            
        ?></code></blockquote>
        
        <p>which should contain code to generate the front page for your site.
        
    </body>
</html>

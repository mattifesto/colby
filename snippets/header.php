<!doctype html>
<html lang="en">
    <head>
        <meta charset="UTF-8">
        <title><?php
            echo htmlspecialchars(self::$args->title,
                ENT_QUOTES,
                'UTF-8');
            ?></title>
        <meta name="description" content="<?php
            echo htmlspecialchars(self::$args->description,
                ENT_QUOTES,
                'UTF-8');
            ?>">
        <link rel="stylesheet"
              href="<?php echo COLBY_SITE_URL;
                    ?>/colby/css/equalize.css">
        <link rel="stylesheet"
              href="<?php echo COLBY_SITE_URL;
                    ?>/colby/css/style.css">
    </head>
    <body>

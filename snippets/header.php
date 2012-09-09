<!doctype html>
<html lang="en">
    <head>
        <meta charset="UTF-8">
        <title><?php echo htmlspecialchars(self::$args->title,
                                           ENT_QUOTES,
                                           'UTF-8'); ?></title>

        <meta name="description"
              content="<?php echo htmlspecialchars(self::$args->description,
                                                   ENT_QUOTES,
                                                   'UTF-8'); ?>">

        <link rel="stylesheet"
              href="<?php echo COLBY_SITE_URL; ?>/colby/css/equalize.css">

        <!--

        This header is meant to be used for very simple pages, such as admin
        pages, that are generally not seen by end users. However there are
        still some basic styles that can help these pages look a little better.

        -->

        <style>
            html
            {
                font-family: "Lucida Grande", Tahoma, Verdana, Arial, sans-serif;
            }

            body
            {
                margin: 20px;
            }

            button,
            input[type="button"],
            input[type="submit"],
            input[type="reset"]
            {
                padding: 2px 6px 3px;
            }

            p
            {
                margin-top: 1.0em;
                margin-bottom: 1.0em;
            }
        </style>
    </head>
    <body>

<?php

// mise en place

$titleHTML = ColbyConvert::textToHTML(self::$args->title);
$descriptionHTML = ColbyConvert::textToHTML(self::$args->description);

?>
<!doctype html>
<html lang="en">
    <head>
        <meta charset="UTF-8">
        <title><?php echo $titleHTML; ?></title>

        <meta name="description"
              content="<?php echo $descriptionHTML; ?>">

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
                margin: 50px 20px 20px;
            }

            body > nav
            {
                position: absolute;
                top: 0px;
                left: 0px;
                width: 100%;
                padding: 5px;
                background-color: LightGray;
                border-bottom: 2px solid DarkGray;
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

            table
            {
                margin: 1.0em 0.0em 3.0em;
                border: 1px solid #ddddff;
                border-spacing: 0px;
                table-layout: fixed;
            }

            td,
            th
            {
                padding: 5px 10px;
                overflow: hidden; /* wrapping behavior */
                text-overflow: ellipsis;
                white-space: nowrap;
            }

            th
            {
                background-color: #eeeeff;
            }

            table tr:nth-child(even)
            {
                background-color: #f8f8ff;
            }
        </style>
    </head>
    <body>
        <nav>
            <ul class="horizontal">
                <li><a href="/admin/">admin</a></li>
                <li><?php echo $titleHTML; ?></li>
            </ul>
        </nav>

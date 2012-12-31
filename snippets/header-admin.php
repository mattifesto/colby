<?php

// mise en place

$titleHTML = $this->titleHTML;
$descriptionHTML = $this->descriptionHTML;

?>
<!doctype html>
<html lang="en">
    <head>
        <meta charset="UTF-8">
        <title><?php echo $titleHTML; ?></title>

        <meta name="description"
              content="<?php echo $descriptionHTML; ?>">

        <link rel="stylesheet"
              type="text/css"
              href="http://fonts.googleapis.com/css?family=Source+Sans+Pro:400,700">

        <link rel="stylesheet"
              type="text/css"
              href="<?php echo COLBY_SITE_URL; ?>/colby/css/equalize.css">

        <link rel="stylesheet"
              type="text/css"
              href="<?php echo COLBY_SITE_URL; ?>/colby/css/shared.css">

        <link rel="stylesheet"
              type="text/css"
              href="<?php echo COLBY_SITE_URL; ?>/colby/css/admin.css">

        <script src="<?php echo COLBY_SITE_URL; ?>/colby/javascript/Colby.js"></script>

        <script src="<?php echo COLBY_SITE_URL; ?>/colby/javascript/ColbyFormManager.js"></script>
    </head>
    <body>
        <nav>
            <ul class="horizontal" style="padding: 5px;">
                <li><a href="<?php echo COLBY_SITE_URL; ?>">home</a></li>
                <li><?php echo $titleHTML; ?></li>
                <li style="float: right;"><?php echo ColbyUser::loginHyperlink(); ?></li>
                <li style="float: right;"><a href="/admin/">admin</a></li>
                <li style="float: right;"><a href="/developer/">developer</a></li>
            </ul>
        </nav>

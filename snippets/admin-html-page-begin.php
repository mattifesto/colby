<?php

$titleHTML = $this->titleHTML;
$descriptionHTML = $this->descriptionHTML;

$stubs = ColbyRequest::decodedStubs();

$adminSelectedClass = (isset($stubs[0]) && $stubs[0] == 'admin') ? 'class="selected"' : '';

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
              href="https://fonts.googleapis.com/css?family=Source+Sans+Pro:400,600,700">

        <link rel="stylesheet"
              type="text/css"
              href="<?php echo COLBY_SITE_URL; ?>/colby/css/equalize.css">

        <link rel="stylesheet"
              type="text/css"
              href="<?php echo COLBY_SITE_URL; ?>/colby/css/shared.css">

        <link rel="stylesheet"
              type="text/css"
              href="<?php echo COLBY_SITE_URL; ?>/colby/css/admin.css">

        <script src="<?php echo COLBY_SITE_URL; ?>/colby/javascript/html5shiv.js"></script>

        <script src="<?php echo COLBY_SITE_URL; ?>/colby/javascript/ColbyEqualize.js"></script>

        <script src="<?php echo COLBY_SITE_URL; ?>/colby/javascript/Colby.js"></script>

        <script src="<?php echo COLBY_SITE_URL; ?>/colby/javascript/ColbyFormManager.js"></script>

        <script src="<?php echo COLBY_SITE_URL; ?>/colby/javascript/ColbySheet.js"></script>
    </head>
    <body>
        <nav class="menubar">
            <ul class="horizontal">
                <li><a href="<?php echo COLBY_SITE_URL; ?>">Home</a></li>
                <li><a href="<?php echo COLBY_SITE_URL; ?>/admin/"
                       <?php echo $adminSelectedClass; ?>>Admin</a></li>

                <?php

                    if ($userRow = ColbyUser::userRow())
                    {
                        ?>

                        <li><?php echo $userRow->facebookName; ?></li>

                        <?php
                    }

                ?>

                <li><?php echo ColbyUser::loginHyperlink(); ?></li>
            </ul>
        </nav>
        <section>
            <nav class="menu-column">
                <ul><?php include Colby::findSnippet('menu-items-admin.php'); ?></ul>
            </nav>

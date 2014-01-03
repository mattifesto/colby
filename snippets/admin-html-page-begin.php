<?php

$stubs = ColbyRequest::decodedStubs();

$adminSelectedClass = (isset($stubs[0]) && $stubs[0] == 'admin') ? 'class="selected"' : '';

?>
<!doctype html>
<html lang="en">
    <head>
        <meta charset="UTF-8">
        <title><?php echo $this->titleHTML; ?></title>

        <meta name="description" content="<?php echo $this->descriptionHTML; ?>">

        <?php foreach ($this->cssURLs as $cssURL) { ?>

            <link rel="stylesheet" href="<?php echo $cssURL; ?>">

        <?php } ?>

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
        <div class="page fixed-page-width">
            <nav class="vertical-menu">
                <ul><?php include Colby::findSnippet('menu-items-admin.php'); ?></ul>
            </nav> <!-- vertical-menu -->
            <div class="content">

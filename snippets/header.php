<?php

// mise en place

$titleHTML = ColbyConvert::textToHTML($this->title);
$descriptionHTML = ColbyConvert::textToHTML($this->description);

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

        <link rel="stylesheet"
              href="<?php echo COLBY_SITE_URL; ?>/colby/css/shared.css">

        <script src="<?php echo COLBY_SITE_URL; ?>/colby/javascript/Colby.js"></script>
    </head>
    <body>
        <nav>
            <ul class="horizontal" style="padding: 5px;">
                <li><a href="<?php echo COLBY_SITE_URL; ?>">home</a></li>
                <li style="float: right;"><?php echo ColbyUser::loginHyperlink(); ?></li>

                <?php

                if ($userRow = ColbyUser::userRow())
                {
                    ?>

                    <li style="float: right;"><?php echo $userRow->facebookName; ?></li>

                    <?php
                }

                ?>

            </ul>
        </nav>

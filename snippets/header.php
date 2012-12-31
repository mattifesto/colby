<?php

// mise en place

global $searchQueryHTML;

$titleHTML = $this->titleHTML;
$descriptionHTML = $this->descriptionHTML;
$searchQueryHTML = isset($searchQueryHTML) ? $searchQueryHTML : '';

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

        <script src="<?php echo COLBY_SITE_URL; ?>/colby/javascript/Colby.js"></script>
    </head>
    <body>
        <nav>
            <ul class="horizontal" style="padding: 5px;">
                <li><a href="<?php echo COLBY_SITE_URL; ?>">home</a></li>
                <li><a href="<?php echo COLBY_SITE_URL . '/blog/'; ?>">blog</a></li>
                <li style="float: right;"><?php echo ColbyUser::loginHyperlink(); ?></li>

                <?php

                if ($userRow = ColbyUser::userRow())
                {
                    ?>

                    <li style="float: right;"><?php echo $userRow->facebookName; ?></li>

                    <?php

                    if ($userRow->hasBeenVerified)
                    {
                        ?>

                        <li style="float: right;"><a href="<?php echo COLBY_SITE_URL . '/admin/'; ?>">admin</a></li>

                        <?php
                    }
                }

                ?>

                <li style="float: right;">
                    <form action="<?php echo COLBY_SITE_URL . '/search/'; ?>">
                        <input type="text" name="query" value="<?php echo $searchQueryHTML; ?>" placeholder="Search">
                    </form>
                </li>
            </ul>
        </nav>

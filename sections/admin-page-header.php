<?php

$stubs = ColbyRequest::decodedStubs();

$adminSelectedClass = (isset($stubs[0]) && $stubs[0] == 'admin') ? 'class="selected"' : '';

CBHTMLOutput::addCSSURL('https://fonts.googleapis.com/css?family=Source+Sans+Pro:400,600,700');
CBHTMLOutput::addCSSURL('https://fonts.googleapis.com/css?family=Source+Code+Pro');
CBHTMLOutput::addCSSURL(CBSystemURL . '/css/equalize.css');
CBHTMLOutput::addCSSURL(CBSystemURL . '/css/shared.css');
CBHTMLOutput::addCSSURL(CBSystemURL . '/css/standard.css');
CBHTMLOutput::addCSSURL(CBSystemURL . '/css/standard-formatted-content.css');
CBHTMLOutput::addCSSURL(CBSystemURL . '/css/admin.css');

CBHTMLOutput::addJavaScriptURL(CBSystemURL . '/javascript/html5shiv.js');
CBHTMLOutput::addJavaScriptURL(CBSystemURL . '/javascript/ColbyEqualize.js');
CBHTMLOutput::addJavaScriptURL(CBSystemURL . '/javascript/Colby.js');
CBHTMLOutput::addJavaScriptURL(CBSystemURL . '/javascript/ColbyFormManager.js');
CBHTMLOutput::addJavaScriptURL(CBSystemURL . '/javascript/ColbySheet.js');

?>

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
    <div class="content">

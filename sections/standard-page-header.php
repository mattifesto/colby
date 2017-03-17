<?php

CBHTMLOutput::addCSSURL(CBSystemURL . '/sections/standard-page-header.css');

?>

<header class="standard-page-header">
    <?php echo cbhtml(CBSitePreferences::siteName()); ?>
</header>

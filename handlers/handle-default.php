<?php

header($_SERVER['SERVER_PROTOCOL'] . ' 404 Not Found');

CBHTMLOutput::begin();
CBHTMLOutput::setTitleHTML('Page Not Found');
CBHTMLOutput::setDescriptionHTML('The page you requested was not found.');

?>

<main style="text-align: center; padding: 40px 10px;">
    <p style="margin-bottom: 40px;">The page you requested was not found.

    <div style="font-size: 1.5em;">
        <?= CBSitePreferences::siteURL(), htmlspecialchars( preg_replace('/([\/\?&])/', ' $1 ', $_SERVER['REQUEST_URI'])) ?>
    </div>
</main>

<?php

CBHTMLOutput::render();

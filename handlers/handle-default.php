<?php

header($_SERVER['SERVER_PROTOCOL'] . ' 404 Not Found');

$page = new ColbyOutputManager();

$page->titleHTML = 'Page Not Found';
$page->descriptionHTML = 'The page you requested was not found.';

$page->begin();

?>

<p>The page you requested was not found.

<p>
    <code><?php

        // put spaces around the following three characters: / ? &
        // to allow for nice line break behavior

        echo COLBY_SITE_URL,
            htmlspecialchars(
                preg_replace('/([\/\?&])/',
                             ' $1 ',
                             $_SERVER['REQUEST_URI']));


    ?></code>

<?php

if (COLBY_SITE_IS_BEING_DEBUGGED)
{
    ?>

    <div style="padding: 0px 50px; margin-top: 100px; font-size: 12px;">
        <pre><?php echo ColbyConvert::textToHTML(var_export($_SERVER, true)); ?></pre>
    </div>

    <?php
}

$page->end();

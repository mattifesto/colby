<?php

header($_SERVER['SERVER_PROTOCOL'] . ' 404 Not Found');

include_once CBSystemDirectory . '/classes/CBHTMLOutput.php';


CBHTMLOutput::setTitleHTML('Page Not Found');
CBHTMLOutput::setDescriptionHTML('The page you requested was not found.');
CBHTMLOutput::begin();

include Colby::findFile('sections/public-page-settings.php');

?>

<main>
    <style scoped>

        main
        {
            text-align: center;
        }

        main h1
        {
            margin: 50px 0;
        }

    </style>

    <h1>The page you requested was not found.</h1>

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

</main>

<?php

if (COLBY_SITE_IS_BEING_DEBUGGED)
{
    ?>

    <div style="padding: 0px 50px; margin-top: 100px; font-size: 12px;">
        <pre><?php echo ColbyConvert::textToHTML(var_export($_SERVER, true)); ?></pre>
    </div>

    <?php
}

CBHTMLOutput::render();

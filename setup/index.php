<?php

// This class defines the smallest amount of stuff to be able to include header.php and footer.php. They must be included from a class with a static $args variable. COLBY_SITE_URL is set to empty so URLs generated using it on this page won't include the full URL, but will work anyway.

define('COLBY_SITE_DIR', $_SERVER['DOCUMENT_ROOT']);
define('COLBY_SITE_URL', '');

class ColbySetup
{
    public static $args;

    public static function begin()
    {
        self::$args = new stdClass();
        self::$args->title = 'Colby Setup';
        self::$args->description = 'This page peforms the initial setup of a new website using Colby.';

        include(__DIR__ . '/../snippets/header.php');

    }

    public static function end()
    {
        include(__DIR__ . '/../snippets/footer.php');
    }
}

ColbySetup::begin();

?>

<h1>Colby Setup</h1>

<?php

if (is_file(COLBY_SITE_DIR . '/index.php'))
{
    echo '<p>This site has already been set up.';

    goto done;
}

?>

<pre><?php echo var_export($_SERVER, true); ?></pre>

<?php

done:

ColbySetup::end();

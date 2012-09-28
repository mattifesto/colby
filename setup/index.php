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

$indexFilename = COLBY_SITE_DIR . '/index.php';
$configurationFilename = COLBY_SITE_DIR . '/colby-configuration.php';
$htaccessFilename = COLBY_SITE_DIR . '/.htaccess';

ColbySetup::begin();

?>

<style>
input[type=text]
{
    width: 400px;
    padding: 2px;
}

dd
{
    margin: 5px 0px 15px;
}
</style>

<h1>Colby Setup</h1>

<?php

if (is_file($indexFilename))
{
    echo '<p>This site has already been set up.';

    goto done;
}

if (is_file($htaccessFilename))
{
    echo '<p style="color: red;">.htaccess already exists but index.php doesn\'t<br>manual setup required';

    goto done;
}

if (is_file($configurationFilename))
{
    echo '<p style="color: red;">colby-configuration.php already exists but index.php doesn\'t<br>manual setup required';

    goto done;
}

?>

<dl>
    <dt>Site URL</dt>
    <dd><input type="text" id="colby-site-url" value="http://<?php echo $_SERVER['SERVER_NAME']; ?>"></dd>
    <dt>Site Name</dt>
    <dd><input type="text" id="colby-site-name" value=""></dd>
    <dt>Site Administrator Email Address</dt>
    <dd><input type="text" id="colby-site-administrator" value=""></dd>
    <dt>Facebook App ID</dt>
    <dd><input type="text" id="colby-facebook-app-id" value=""></dd>
    <dt>Facebook App Secret</dt>
    <dd><input type="text" id="colby-facebook-app-secret" value=""></dd>
    <dt>MySQL Host</dt>
    <dd><input type="text" id="colby-mysql-host" value=""></dd>
    <dt>MySQL Database</dt>
    <dd><input type="text" id="colby-mysql-database" value=""></dd>
    <dt>MySQL User</dt>
    <dd><input type="text" id="colby-mysql-user" value=""></dd>
    <dt>MySQL Password</dt>
    <dd><input type="text" id="colby-mysql-password" value=""></dd>
    <dt>Developer Information</dt>
    <dd><input type="checkbox" id="colby-site-is-being-debugged"> site is being debugged</dd>
</dl>

<?php

done:

ColbySetup::end();

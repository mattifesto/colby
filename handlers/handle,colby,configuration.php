<?php

$args = new stdClass();
$args->title = 'Colby Configuration';
$args->description = 'Use this page to configure Colby.';

ColbyPage::beginAdmin($args);

?>

<style>
h1
{
    margin-bottom: 1.0em;
}

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

<h1>Colby Configuration</h1>

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
    <dt>First Verified User Facebook ID</dt>
    <dd><input type="text" id="colby-facebook-first-verified-user-id" value=""></dd>
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

<button onclick="">Install Colby</button>

<?php

ColbyPage::end();


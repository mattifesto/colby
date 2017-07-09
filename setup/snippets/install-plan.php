
<h1>Welcome to Colby</h1>

<?php

if (ColbyInstaller::$exception) {
    ?>

    <div class="exception">
        <p>Exception: <?= cbhtml(ColbyInstaller::$exception->getMessage()) ?>
        <p>File: <?= cbhtml(ColbyInstaller::$exception->getFile()) ?>
        <p>Line: <?= cbhtml(ColbyInstaller::$exception->getLine()) ?>
    </div>

    <?php
}
?>

<p>This page will allow you perform the initial site installation. If you choose to continue some template files will be placed in your site's root directory.

<?php if (self::shouldPerformAFullInstallation()) { ?>

    <h2>Full Installation</h2>

    <p>This appears to be a new website so Colby will be installed so that all requests except for allowed direct file requests will be handled by the Colby system.

<?php } else { ?>

    <h2>Partial Installation</h2>

    <p>It appears that this website is already serving content. Colby will be installed in a way such that any given request can be optionally handled using the Colby system. Individual requests can use Colby and the <code>.htaccess</code> file can be customized to handle groups of requests using Colby.

<?php } ?>

<h2>Properties</h2>

<?php $p = ColbyInstaller::$properties ?>

<form action="" method="post">
    <div class="property">
        <div>Site Domain Name</div>
        <input type="text" name="siteDomainName" value="<?= cbhtml($p->siteDomainName) ?>">
    </div>
    <div class="property">
        <div>MySQL Host</div>
        <input type="text" name="mysqlHost" value="<?= cbhtml($p->mysqlHost) ?>">
    </div>
    <div class="property">
        <div>MySQL User</div>
        <input type="text" name="mysqlUser" value="<?= cbhtml($p->mysqlUser) ?>">
    </div>
    <div class="property">
        <div>MySQL Password</div>
        <input type="text" name="mysqlPassword" value="<?= cbhtml($p->mysqlPassword) ?>">
    </div>
    <div class="property">
        <div>MySQL Database</div>
        <input type="text" name="mysqlDatabase" value="<?= cbhtml($p->mysqlDatabase) ?>">
    </div>
    <div class="property">
        <div>Facebook App ID</div>
        <input type="text" name="facebookAppID" value="<?= cbhtml($p->facebookAppID) ?>">
    </div>
    <div class="property">
        <div>Facebook App Secret</div>
        <input type="text" name="facebookAppSecret" value="<?= cbhtml($p->facebookAppSecret) ?>">
    </div>
    <div class="property">
        <div>Email Address</div>
        <input type="text" name="emailAddress" value="<?= cbhtml($p->emailAddress) ?>">
    </div>
    <div class="property">
        <div>Email Name</div>
        <input type="text" name="emailName" value="<?= cbhtml($p->emailName) ?>">
    </div>
    <div class="property">
        <div>SMTP Server Domain Name</div>
        <input type="text" name="smtpDomainName" value="<?= cbhtml($p->smtpDomainName) ?>">
    </div>
    <div class="property">
        <div>SMTP Server Port</div>
        <input type="text" name="smtpPort" value="<?= cbhtml($p->smtpPort) ?>">
    </div>
    <div class="property">
        <div>SMTP Server Security</div>
        <input type="text" name="smtpSecurity" value="<?= cbhtml($p->smtpSecurity) ?>">
    </div>
    <div class="property">
        <div>SMTP User</div>
        <input type="text" name="smtpUser" value="<?= cbhtml($p->smtpUser) ?>">
    </div>
    <div class="property">
        <div>SMTP Password</div>
        <input type="text" name="smtpPassword" value="<?= cbhtml($p->smtpPassword) ?>">
    </div>
    <input type="submit">
</form>

<h2>Details</h2>

<dl>
    <dt>.htaccess

    <?php if (self::shouldPerformAFullInstallation()) { ?>

        <dd>This file will redirect all requests to the Colby system and deny direct access to certain protected files like Git repostiory files, individual PHP files, and data files.

    <?php } else { ?>

        <dd>The existing <code>.htaccess</code> file will have entries added to allow Colby administrative URLs to be handled by Colby. Direct access will be denied for <code>.php</code> files in the Colby directory and no access will be allowed for Git repository files anywhere on the entire website. Access to other file types like <code>.json</code> and <code>.data</code> will be denied as well as access to special directories like <code>/tmp</code>.

    <?php } ?>

    <dt>.gitignore
    <dd>This file will configure Git to ignore certain files and directories that will be created when using a Colby site but that shouldn't be checked in. One example is all of the files in the data directory.

    <dt>colby-configuration.php
    <dd>The setup process will create this file, which you will need to edit, to provide site metadata and database connection information. This file will be ignored by Git and needs to be manually created for each instance of your site. Site instances, such as development, test, and production will each probably have different values for the constants set in this file. You will be reminded to edit the file after this initial setup process is complete.

    <dt>data directory
    <dd>A directory named 'data' will be created in the website root. This directory will be used to hold the archives. There will be usually one archive per unit of data. A unit of data may be a page, blog post, product, user, but is almost always associated with something that has its own URL and web page.

    <dt>favicon.gif
    <dt>favicon.ico
    <dd>Zero length files will be created with these names because the files are often requested by browsers and it is faster to have zero length files available than to run a full Colby request just to generate a 404 error. When these files are zero length browsers treat them as if they didn't exist at all, so it's still effectively a 404, only faster.

    <dt>index.php
    <dd>Any URL that doesn't refer to an actual file or references a file for which direct access is not allowed, such as &ldquo;.php&rdquo; files, will be redirected to this file which will send the URLs through the Colby system to generate content.

    <dt>site-configuration.php
    <dd>The setup process will create this file which may be edited to provide configuration settings and perform actions that are shared between all instances of your site. If you need to load libraries, this is the place to do it.

    <dt>version.php
    <dd>This file contains the version number for the website. It should be incremented by one and checked in for each release. The version number should always be a whole number in the same way the Firefox and Chrome now use only whole number versions that will potentially go quite high.
</dl>

<p>After you complete the initial installation there are a few more required steps such as editing the colby-configuration.php file mentioned above and installing the database. You will be guided to complete these steps. After clicking on the following link you will be redirected and to another page and this page will no longer be needed or available.

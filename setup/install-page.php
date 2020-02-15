
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

<p>Installation will create database tables and some files in your site's root
directory.

<?php if (self::shouldPerformAFullInstallation()) { ?>

    <h2>Full Installation</h2>

    <p>This appears to be a new website so Colby will be installed so that all
    requests except for allowed direct file requests will be handled by the
    Colby system.

<?php } else { ?>

    <h2>Partial Installation</h2>

    <p>It appears that this website is already serving content. Colby will be
    installed in a way such that any given request can be optionally handled
    using the Colby system. Individual requests can use Colby and the
    <code>.htaccess</code> file can be customized to handle groups of requests
    using Colby.

<?php } ?>

<h2>Properties</h2>

<?php $p = ColbyInstaller::$properties ?>

<form action="" method="post">
    <div class="property">
        <label>Site Domain Name</label>
        <input
            type="text"
            name="siteDomainName"
            value="<?= cbhtml($p->siteDomainName) ?>"
        >
    </div>
    <div class="property">
        <label>Site Class Prefix</label>
        <input
            type="text"
            name="siteClassPrefix"
            value="<?= cbhtml($p->siteClassPrefix) ?>"
        >
    </div>
    <div class="property">
        <label>MySQL Host</label>
        <input
            type="text"
            name="mysqlHost"
            value="<?= cbhtml($p->mysqlHost) ?>"
        >
    </div>
    <div class="property">
        <label>MySQL User</label>
        <input
            type="text"
            name="mysqlUser"
            value="<?= cbhtml($p->mysqlUser) ?>"
        >
    </div>

    <div class="property">
        <label>MySQL Password</label>
        <input
            type="password"
            name="mysqlPassword"
            value="<?= cbhtml($p->mysqlPassword) ?>"
        >
    </div>

    <div class="property">
        <label>MySQL Database</label>
        <input
            type="text"
            name="mysqlDatabase"
            value="<?= cbhtml($p->mysqlDatabase) ?>"
        >
    </div>

    <!-- first user -->

    <div class="property">
        <label>Developer Email Address</label>
        <input
            type="email"
            name="developerEmailAddress"
            value="<?= cbhtml($p->developerEmailAddress) ?>"
        >
    </div>

    <div class="property">
        <label>Developer Password</label>
        <input
            type="password"
            name="developerPassword"
            value="<?= cbhtml($p->developerPassword) ?>"
        >
    </div>

    <div class="property">
        <label>Re-enter Developer Password</label>
        <input
            type="password"
            name="developerPassword2"
            value="<?= cbhtml($p->developerPassword2) ?>"
        >
    </div>

    <!-- submit -->

    <input
        type="submit"
        value="Submit"
    >

</form>

<h2>Installed Files</h2>

<dl>
    <dt>.htaccess

    <?php if (self::shouldPerformAFullInstallation()) { ?>

        <dd>This file will redirect all requests to the Colby system and deny
        direct access to certain protected files like Git repostiory files,
        individual PHP files, and data files.

    <?php } else { ?>

        <dd>The existing <code>.htaccess</code> file will have entries added to
        allow Colby administrative URLs to be handled by Colby. Direct access
        will be denied for <code>.php</code> files in the Colby directory and no
        access will be allowed for Git repository files anywhere on the entire
        website. Access to other file types like <code>.json</code> and
        <code>.data</code> will be denied as well as access to special
        directories like <code>/tmp</code>.

    <?php } ?>

    <dt>.gitignore
    <dd>This file will configure Git to ignore certain files and directories
    that will be created when using a Colby site but that shouldn't be checked
    in. One example is all of the files in the data directory.

    <dt>colby-configuration.php
    <dd>This file provides site metadata and database connection information.
    This file will be ignored by Git and needs to be created for each instance
    of your site. Site instances, such as development, test, and production will
    have different values for the constants set in this file.

    <dt>data directory
    <dd>A directory named &ldquo;data&rdquo; will be created to hold file based
    information for models. This includes uploaded files including images.

    <dt>favicon.gif
    <dt>favicon.ico
    <dd>Zero length files will be created with these names because the files are
    often requested by browsers and it is faster to have zero length files
    available than to run a full Colby request just to generate a 404 error.
    When these files are zero length browsers treat them as if they didn't exist
    at all, so it's still effectively a 404, only faster.

    <dt>index.php
    <dd>Any URL that doesn't refer to an actual file or references a file for
    which direct access is not allowed, such as &ldquo;.php&rdquo; files, will
    be redirected to this file which will send the URLs through the Colby system
    to generate a response.

    <dt>site-configuration.php
    <dd>This file may be edited to provide settings, libraries, and perform
    actions that are shared between all instances of your site.

    <dt>version.php
    <dd>This file contains the version number for the website. The version
    number is incremented by a whole number for each new version but may use dot
    versions for bug fixes.
</dl>

<h2>Installed Classes</h2>
<p>Some classes are required for you to be able to create and view pages.
Initial versions classes will be created for you. You can rename and edit these
classes.

<dl>
    <dt>CBXPageFrame
    <dd>A page frame is shared by many pages to provide the header, footer, and
    other content not specific to a single page. A site may offer different
    frames for different types of pages. For instance, blog posts may use a
    different frame than press releases.

    <dt>CBXPageSettings
    <dd>A page settings class is shared by many pages to provide the most basic
    settings of a page such as whether a page is responsive, CSS equalization,
    and analytics. A page settings class does not render any content. While a
    site can offer multiple page settings classes, in many cases, a site will
    have just one page settings class.

    <dt>CBXPageTemplate
    <dd>A page template provides spec that is a starting point for a new web
    page. When you create a page in the admin area you will choose from a list
    of available page templates.
</dl>

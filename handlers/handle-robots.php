<?php

header("Content-Type: text/plain");

?>
Sitemap: <?= cbsiteurl() . '/sitemap.xml' ?>

<?php

/**
 * Robots are not allowed if they are disabled in site preferences or if the
 * request domain is not the primary domain.
 */

if (
    CBSitePreferences::disallowRobots() ||
    CBRequest::requestDomain() !== CBConfiguration::primaryDomain()
) {

    echo <<<EOT

    User-agent: *
    Disallow: /

    EOT;

} else {

    /**
     * @deprecated 2022_01_25
     *
     *      I'm pretty sure the /api/ URL below is no longer used and can be
     *      removed.
     */

    echo <<<EOT

    User-agent: *
    Disallow: /admin/
    Disallow: /api/

    EOT;

}

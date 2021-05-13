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

?>

User-agent: *
Disallow: /

<?php } else { ?>

User-agent: *
Disallow: /admin/
Disallow: /api/
Disallow: /developer/

<?php }

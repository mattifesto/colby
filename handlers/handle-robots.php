<?php

header("Content-Type: text/plain");

?>
Sitemap: <?= CBSitePreferences::siteURL() . '/sitemap.xml' ?>

<?php

if (CBSitePreferences::disallowRobots()) { ?>

User-agent: *
Disallow: /

<?php } else { ?>

User-agent: *
Disallow: /admin/
Disallow: /api/
Disallow: /developer/

<?php }

<?php

header("Content-Type: text/plain");

?>
Sitemap: <?= CBSiteURL . '/sitemap.xml' ?>

<?php

if (CBSitePreferences::disallowRobots()) { ?>

User-agent: *
Disallow: /

<?php } else { ?>

User-agent: *
Disallow: /admin/
Disallow: /colby/
Disallow: /developer/

<?php }

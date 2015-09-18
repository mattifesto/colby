<?php

header('Content-type: text/xml');

$URLs = [CBSiteURL . '/'];

if (defined('CBSiteConfiguration::pageProviderClassNames')) {
    foreach (CBSiteConfiguration::pageProviderClassNames as $className) {
        if (is_callable($function = "$className::pageURLs")) {
            $URLs = array_merge($URLs, call_user_func($function));
        }
    }
}

?>
<?xml version="1.0" encoding="utf-8"?>
<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9"
    xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance"
    xsi:schemaLocation="http://www.sitemaps.org/schemas/sitemap/0.9 http://www.sitemaps.org/schemas/sitemap/0.9/sitemap.xsd">
   <?php

   array_walk($URLs, function($URL) {
       echo "<url><loc>$URL</loc></url>";
   });

   ?>
</urlset>

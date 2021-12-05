<?php

header('Content-type: text/xml');

echo '<?xml version="1.0" encoding="utf-8" ?>';

$sitemapInformation = CBModels::fetchSitemapInformaton();

?>
<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">
   <?php

   array_walk(
       $sitemapInformation,
       function (
           $page
       ) {
           echo '<url>';
           echo "<loc>{$page->CBModels_sitemapInformation_URL}</loc>";

           if (
               $page->CBModels_sitemapInformation_modified !== null
           ) {
               $lastmodW3C = gmdate(
                   DateTime::W3C,
                   $page->CBModels_sitemapInformation_modified
               );

               echo "<lastmod>{$lastmodW3C}</lastmod>";
           }

           echo '</url>';
       }
   );

   ?>
</urlset>

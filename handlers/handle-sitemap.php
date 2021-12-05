<?php

header('Content-type: text/xml');

$frontPageSummaryModel = [
    (object)[
        'URI' => '/',
    ],
];

$SQL = <<<EOT

    SELECT  `keyValueData`
    FROM    `ColbyPages`
    WHERE   `published` IS NOT NULL

EOT;

$pageSummaryModels = CBDB::SQLToArray(
    $SQL,
    [
        'valueIsJSON' => true
    ]
);

$pageSummaryModels = array_merge(
    $frontPageSummaryModel,
    $pageSummaryModels
);

echo '<?xml version="1.0" encoding="utf-8" ?>';

?>
<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">
   <?php

   array_walk(
       $pageSummaryModels,
       function (
           $model
       ) {
           if (
               empty($model->URI)
           ) {
               return;
           } else if (
               $model->URI === '/'
           ) {
               $URL = cbsiteurl() . '/';
           } else {
               $URL = cbsiteurl() . "/{$model->URI}/";
           }

           echo '<url>';
           echo "<loc>{$URL}</loc>";

           $lastmodTimestamp = (
               empty($model->updated) ?
               null :
               (int)$model->updated
           );

           if (
               !empty($lastmodTimestamp)
           ) {
               $lastmodW3C = gmdate(
                   DateTime::W3C,
                   $lastmodTimestamp
               );

               echo "<lastmod>{$lastmodW3C}</lastmod>";
           }

           echo '</url>';
       }
   );

   ?>
</urlset>

<?php

$page = ColbyOutputManager::beginVerifiedUserPage('Blog Post Types', 'Developer tools for blog post types.');

$builtInPostTypeDataFiles = glob(COLBY_SITE_DIRECTORY . '/colby/handlers/handle,admin,blog,*.data');

foreach ($builtInPostTypeDataFiles as $postTypeDataFile)
{
    $postTypeData = unserialize(file_get_contents($postTypeDataFile));

    ?>

    <h1 style="font-size: 1.5em;"><?php echo $postTypeData->name; ?></h1>
    <p><?php echo $postTypeData->description; ?>

    <?php
}

$siteSpecificPostTypeDataFiles = glob(COLBY_SITE_DIRECTORY . '/handlers/handle,admin,blog,*.data');

foreach ($siteSpecificPostTypeDataFiles as $postTypeDataFile)
{
    $postTypeData = unserialize(file_get_contents($postTypeDataFile));

    ?>

    <h1 style="font-size: 1.5em;"><?php echo $postTypeData->name; ?><h1>
    <p><?php echo $postTypeData->description; ?>

    <?php
}

$page->end();

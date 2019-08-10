<?php

/**
 * @deprecated 2019_08_10
 *
 *      This file only exists to enable admin page redirects on sites that
 *      haven't been updated. This file can be removed as soon as all sites have
 *      been updated.
 */

$adminClassName = cb_query_string_value('class');
$URL = cbsiteurl() . CBAdmin::getAdminPageURL($adminClassName);

header("Location: {$URL}");

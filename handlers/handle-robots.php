<?php

(function ()
{
    header(
        "Content-Type: text/plain"
    );

    $sitemapURL =
    cbsiteurl() .
    '/sitemap.xml';

    echo <<<EOT
    Sitemap: ${sitemapURL}

    EOT;

    /**
     * Robots are not allowed if they are disabled in site preferences or if the
     * request domain is not the primary domain.
     */

    $theRequestDomainIsNotThePrimaryDomain =
    CBRequest::requestDomain() !==
    CBConfiguration::primaryDomain();

    $robotsAreNotAllowed =
    CBSitePreferences::disallowRobots() ||
    $theRequestDomainIsNotThePrimaryDomain;

    if (
        $robotsAreNotAllowed
    ) {
        echo <<<EOT

        User-agent: *
        Disallow: /

        EOT;
    }

    else
    {
        echo <<<EOT

        User-agent: *
        Disallow: /admin/

        EOT;
    }
}
)();

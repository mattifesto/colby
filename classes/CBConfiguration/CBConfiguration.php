<?php

/**
 * @deprecated 2021_05_29
 *
 *      This class is moving to CB_Configuration.
 */
final class
CBConfiguration {

        /* -- functions -- */



        /**
         * @TODO 2021_05_09
         *
         *      The CBSiteURL constant should be deprecated and replaced with
         *      CBConfiguration_primaryDomain. CBSiteURL suggests that something
         *      other than https is supported, which it isn't, or that one could
         *      specify more than just a domain, which one can't.
         *
         * @NOTE
         *
         *      Use CBRequest::requestDomain() to get the domain used by the
         *      current request which may be a secondary domain.
         *
         * @return string
         *
         *      2023-07-18
         *      Matt Calkins
         *
         *      This function may now return an empty string if it has no solid
         *      idea of what its primary domain is. This is the case when it is
         *      running directly from composer during development or when
         *      running behind a load balancer which is determining the primary
         *      domain. In the future, this will probably become the most
         *      common return value.
         */
        static function
        primaryDomain(
        ): string
        {
            $configurationSpec = CB_Configuration::fetchConfigurationSpec();

            $primaryDomain = '';

            if (
                $configurationSpec !== null
            ) {
                $primaryDomain =
                CB_Configuration::getPrimaryWebsiteDomain(
                    $configurationSpec
                );
            }
            else if (
                defined('CBSiteURL')
            ) {
                /* deprecated */

                $primaryDomain =
                explode(
                    '//',
                    CBSiteURL
                )[1];
            }

            return $primaryDomain;
        }
        /* primaryDomain() */



        /**
         * @return bool
         */
        static function
        secondaryDomainsShouldRedirectToPrimaryDomain(
        ): string {
            $configurationSpec = CB_Configuration::fetchConfigurationSpec();

            if ($configurationSpec === null) {
                /* deprecated */
                $isDefined = defined(
                    'CBConfiguration_secondaryDomainsShouldRedirectToPrimaryDomain'
                );

                return (
                    $isDefined &&
                    CBConfiguration_secondaryDomainsShouldRedirectToPrimaryDomain
                );
            } else {
                return CB_Configuration::getSecondaryDomainsShouldRedirectToPrimaryDomain(
                    $configurationSpec
                );
            }
        }
        /* secondaryDomainsShouldRedirectToPrimaryDomain() */

}

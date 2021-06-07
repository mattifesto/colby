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
         */
        static function
        primaryDomain(
        ): string {
            $configurationSpec = CB_Configuration::fetchConfigurationSpec();

            if ($configurationSpec === null) {
                /* deprecated */
                return explode(
                    '//',
                    CBSiteURL
                )[1];
            } else {
                return CB_Configuration::getPrimaryWebsiteDomain(
                    $configurationSpec
                );
            }
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

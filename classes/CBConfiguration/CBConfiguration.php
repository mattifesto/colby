<?php

final class
CBConfiguration {

        /**
         * @TODO 2021_05_09
         *
         *      The CBSiteURL constant should be deprecated and replaced with
         *      CBConfiguration_primaryDomain. CBSiteURL suggests that something
         *      other than https is supported, which it isn't, or that one could
         *      specify more than just a domain, which one can't.
         *
         * @return string
         */
        static function
        primaryDomain(
        ): string {
            return explode(
                '//',
                CBSiteURL 
            )[1];
        }
        /* primaryDomain() */



        /**
         * @return bool
         */
        static function
        secondaryDomainsShouldRedirectToPrimaryDomain(
        ): string {
            $isDefined = defined(
                'CBConfiguration_secondaryDomainsShouldRedirectToPrimaryDomain'
            );

            return (
                $isDefined &&
                CBConfiguration_secondaryDomainsShouldRedirectToPrimaryDomain
            );
        }
        /* secondaryDomainsShouldRedirectToPrimaryDomain() */

}

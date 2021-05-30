<?php

final class
CB_Configuration {

        /* -- accessors -- */



        /**
         * @param object $model
         *
         * @return string
         */
        static function
        getServerSpecificWebsiteDomain(
            stdClass $model
        ): string {
            return $model->CB_Configuration_serverSpecificWebsiteDomain;
        }
        /* getServerSpecificWebsiteDomain() */



        /**
         * @param object $model
         * @param string $serverSpecificWebsiteDomain
         *
         * @return void
         */
        static function
        setServerSpecificWebsiteDomain(
            stdClass $model,
            string $serverSpecificWebsiteDomain
        ): void {
            $model->CB_Configuration_serverSpecificWebsiteDomain = (
                $serverSpecificWebsiteDomain
            );
        }
        /* setServerSpecificWebsiteDomain() */

}

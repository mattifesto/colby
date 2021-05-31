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
        getPrimaryWebsiteDomain(
            stdClass $model
        ): string {
            return CBModel::valueToString(
                $model,
                'CB_Configuration_primaryWebsiteDomain'
            );
        }
        /* getPrimaryWebsiteDomain() */



        /**
         * @param object $model
         * @param string $primaryWebsiteDomain
         *
         * @return void
         */
        static function
        setPrimaryWebsiteDomain(
            stdClass $model,
            string $primaryWebsiteDomain
        ): void {
            $model->CB_Configuration_primaryWebsiteDomain = (
                $primaryWebsiteDomain
            );
        }
        /* setPrimaryWebsiteDomain() */



        /**
         * @param object $model
         *
         * @return string
         */
        static function
        getServerSpecificWebsiteDomain(
            stdClass $model
        ): string {
            return CBModel::valueToString(
                $model,
                'CB_Configuration_serverSpecificWebsiteDomain'
            );
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

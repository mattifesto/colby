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
        getDatabaseHost(
            stdClass $model
        ): string {
            return CBModel::valueToString(
                $model,
                'CB_Configuration_databaseHost'
            );
        }
        /* getDatabaseHost() */



        /**
         * @param object $model
         * @param string $databaseHost
         *
         * @return void
         */
        static function
        setDatabaseHost(
            stdClass $model,
            string $databaseHost
        ): void {
            $model->CB_Configuration_databaseHost = (
                $databaseHost
            );
        }
        /* setDatabaseHost() */



        /**
         * @param object $model
         *
         * @return string
         */
        static function
        getDatabaseName(
            stdClass $model
        ): string {
            return CBModel::valueToString(
                $model,
                'CB_Configuration_databaseName'
            );
        }
        /* getDatabaseName() */



        /**
         * @param object $model
         * @param string $databaseName
         *
         * @return void
         */
        static function
        setDatabaseName(
            stdClass $model,
            string $databaseName
        ): void {
            $model->CB_Configuration_databaseName = (
                $databaseName
            );
        }
        /* setDatabaseName() */



        /**
         * @param object $model
         *
         * @return string
         */
        static function
        getDatabasePassword(
            stdClass $model
        ): string {
            return CBModel::valueToString(
                $model,
                'CB_Configuration_databasePassword'
            );
        }
        /* getDatabasePassword() */



        /**
         * @param object $model
         * @param string $databasePassword
         *
         * @return void
         */
        static function
        setDatabasePassword(
            stdClass $model,
            string $databasePassword
        ): void {
            $model->CB_Configuration_databasePassword = (
                $databasePassword
            );
        }
        /* setDatabasePassword() */



        /**
         * @param object $model
         *
         * @return string
         */
        static function
        getDatabaseUsername(
            stdClass $model
        ): string {
            return CBModel::valueToString(
                $model,
                'CB_Configuration_databaseUsername'
            );
        }
        /* getDatabaseUsername() */



        /**
         * @param object $model
         * @param string $databaseUsername
         *
         * @return void
         */
        static function
        setDatabaseUsername(
            stdClass $model,
            string $databaseUsername
        ): void {
            $model->CB_Configuration_databaseUsername = (
                $databaseUsername
            );
        }
        /* setDatabaseUsername() */



        /**
         * @param object $model
         *
         * @return string
         */
        static function
        getPrimaryAdministratorEmailAddress(
            stdClass $model
        ): string {
            return CBModel::valueToString(
                $model,
                'CB_Configuration_primaryAdministratorEmailAddress'
            );
        }
        /* getPrimaryAdministratorEmailAddress() */



        /**
         * @param object $model
         * @param string $primaryAdministratorEmailAddress
         *
         * @return void
         */
        static function
        setPrimaryAdministratorEmailAddress(
            stdClass $model,
            string $primaryAdministratorEmailAddress
        ): void {
            $model->CB_Configuration_primaryAdministratorEmailAddress = (
                $primaryAdministratorEmailAddress
            );
        }
        /* setPrimaryAdministratorEmailAddress() */



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
         * @return [string]
         */
        static function
        getSecondaryWebsiteDomains(
            stdClass $model
        ): array {
            return CBModel::valueToArray(
                $model,
                'CB_Configuration_secondaryWebsiteDomains'
            );
        }
        /* getSecondaryWebsiteDomains() */



        /**
         * @param object $model
         * @param [string] $secondaryWebsiteDomains
         *
         * @return void
         */
        static function
        setSecondaryWebsiteDomains(
            stdClass $model,
            array $secondaryWebsiteDomains
        ): void {
            $model->CB_Configuration_secondaryWebsiteDomains = (
                $secondaryWebsiteDomains
            );
        }
        /* setSecondaryWebsiteDomains() */



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



        /* -- functions -- */



        /**
         * @return object|null
         *
         *      Returns the configuration spec if the spec file exists, null
         *      if it does not.
         */
        static function
        fetchConfigurationSpec(
        ): ?stdClass {
            $projectDirectory = cb_project_directory();

            if ($projectDirectory === null) {
                return null;
            }

            $configurationFilename = (
                "{$projectDirectory}/cb_configuration.json"
            );

            if (
                !file_exists($configurationFilename)
            ) {
                return null;
            }

            $specAsJSON = file_get_contents(
                $configurationFilename
            );

            return CBConvert::JSONToValue(
                $specAsJSON
            );
        }
        /* fetchConfigurationSpec() */

}

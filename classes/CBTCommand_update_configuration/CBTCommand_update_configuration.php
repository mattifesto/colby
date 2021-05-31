<?php

final class
CBTCommand_update_configuration {

    /* -- cbt interfaces -- */



    /**
     * @return void
     */
    static function
    cbt_execute(
    ): void {
        $configurationSpec = CBModel::createSpec(
            'CB_Configuration'
        );



        /* server specific website domain */

        $serverSpecificWebsiteDomain = (
            CBTCommand_update_configuration::askForServerSpecificWebsiteDomain()
        );

        CB_Configuration::setServerSpecificWebsiteDomain(
            $configurationSpec,
            $serverSpecificWebsiteDomain
        );



        /* primary website domain */

        $primaryWebsiteDomain = (
            CBTCommand_update_configuration::askForPrimaryWebsiteDomain()
        );

        if ($primaryWebsiteDomain === '') {
            $primaryWebsiteDomain = $serverSpecificWebsiteDomain;
        }

        CB_Configuration::setPrimaryWebsiteDomain(
            $configurationSpec,
            $primaryWebsiteDomain
        );



        /* secondary website domains */

        $secondaryWebsiteDomains = (
            CBTCommand_update_configuration::askForSecondaryWebsiteDomains()
        );

        CB_Configuration::setSecondaryWebsiteDomains(
            $configurationSpec,
            $secondaryWebsiteDomains
        );



        /* database name */

        $serverSpecificWebsiteReverseDomain = (
            CBConvert::domainToReverseDomain(
                $serverSpecificWebsiteDomain
            )
        );

        CB_Configuration::setDatabaseName(
            $configurationSpec,
            "{$serverSpecificWebsiteReverseDomain}_database"
        );



        /* database username */

        CB_Configuration::setDatabaseUserName(
            $configurationSpec,
            CBDB::generateDatabaseUsername()
        );



        /* database password */

        CB_Configuration::setDatabasePassword(
            $configurationSpec,
            CBDB::generateDatabasePassword()
        );



        /* write configuration file */

        file_put_contents(
            cbsitedir() . "/../cb_configuration.json",
            CBConvert::valueToPrettyJSON(
                $configurationSpec
            ) . "\n"
        );
    }
    /* cbt_execute() */



    /* -- functions -- */



    /**
     * @return string
     *
     *      Returns the primary website domain or an empty string if the user
     *      wants to use the server specific website domain as the primary
     *      domain.
     */
    static function
    askForPrimaryWebsiteDomain(
    ): string {
        echo <<<EOT

            The primary website domain is the most preferred domain for a
            website. For the Mattifesto production website it is
            "mattifesto.com". Development and test websites most often just use
            the server specific website domain as the primary website domain.

            If this website instance does not have a primary website domain just
            press return.

        EOT;

        while (true) {
            echo "\nenter the primary website domain or press return: ";

            $result = CBTCommand_update_configuration::inputDomain();

            if (
                $result->value === '' ||
                $result->isValidDomain
            ) {
                return $result->value;
            }
        }
    }
    /* askForPrimaryWebsiteDomain() */



    /**
     * @return [string]
     */
    static function
    askForSecondaryWebsiteDomains(
    ): array {
        echo <<<EOT

            Secondary website domains are domains that your website accepts and
            are generally redirected to the primary domain. The most common
            example of this is if "mattifesto.com" is your primary domian,
            "www.mattifesto.com" will probably be a secondary domain. Or vice
            versa if "www.mattifesto.com" is your primary domain.

            If this website instance does not have any secondary website domains
            just press return.

        EOT;


        while (true) {
            echo "\nenter the secondary website domains or press return: ";

            $result = CBTCommand_update_configuration::inputMultipleDomains();

            if (
                $result->firstInvalidDomainIndex === null
            ) {
                return $result->values;
            }
        }
    }
    /* askForSecondaryWebsiteDomains() */



    /**
     * @return string
     */
    static function
    askForServerSpecificWebsiteDomain(
    ): string {
        echo <<<EOT

            The server specific website domain is a domain that identifies this
            website as it exists only on this server. Once the website is no
            longer needed on this server this domain will never be used again.
            The server specific domain is intended to indicate the purpose of
            this website instance and allows access to this specific website
            instance if the website's primary domain is not server specific.

            Development and test websites will often have only a server specific
            domain and it will be used as the primary domain as well.

            Production sites will usually have a different primary domain that
            moves with the website from server to server.

            The domain "mattifesto.ld17.mtfs.us" is an example of a Mattifesto
            website server specific domain and indicates that the website
            instance is used for development and is on the 17th web server which
            is a local network development web server.

        EOT;

        while (true) {
            echo "\nenter server specific domain: ";

            $result = CBTCommand_update_configuration::inputDomain();

            if ($result->isValidDomain) {
                return $result->value;
            }
        }
    }
    /* askForServerSpecificWebsiteDomain() */



    /**
     * This function presents no user interface, so the calling function should.
     * It simply allows the user to enter a string.
     *
     * @return stdClass
     *
     *      {
     *          value: string
     *          isValidDomain: bool
     *      }
     */
    static function
    inputDomain(
    ): stdClass {
        $value = (
            trim(
                fgets(STDIN),
            )
        );

        $result = filter_var(
            $value,
            FILTER_VALIDATE_DOMAIN,
            FILTER_FLAG_HOSTNAME
        );

        return (object)[
            'value' => $value,
            'isValidDomain' => $result !== false,
        ];
    }
    /* inputDomain() */



    /**
     * This function presents no user interface, so the calling function should.
     * It simply allows the user to enter a string.
     *
     * @return stdClass
     *
     *      {
     *          values: []
     *          firstInvalidDomainIndex: int|null
     *      }
     */
    static function
    inputMultipleDomains(
    ): stdClass {
        $value = (
            trim(
                fgets(STDIN),
            )
        );

        $values = preg_split(
            '/[\s,]+/',
            $value,
            -1,
            PREG_SPLIT_NO_EMPTY
        );

        $returnValue = (object)[
            'values' => array_values(
                $values
            ),
            'firstInvalidDomainIndex' => null,
        ];

        for (
            $index = 0;
            $index < count($values);
            $index += 1
        ) {
            $domain = $values[$index];

            $result = filter_var(
                $domain,
                FILTER_VALIDATE_DOMAIN,
                FILTER_FLAG_HOSTNAME
            );

            if ($result === false) {
                $returnValue->firstInvalidDomainIndex = $index;

                return $returnValue;
            }
        }

        return $returnValue;
    }
    /* inputMultipleDomains() */

}

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

        $serverSpecificWebsiteDomain = (
            CBTCommand_update_configuration::askForServerSpecificWebsiteDomain()
        );

        CB_Configuration::setServerSpecificWebsiteDomain(
            $configurationSpec,
            $serverSpecificWebsiteDomain
        );

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

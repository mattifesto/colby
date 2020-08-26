<?php

final class CBSetup {

    /* -- CBAjax interfaces -- -- -- -- -- */



    /**
     * @param object $args
     *
     *      {
     *          developerEmailAddress: string
     *          developerPassword1: string
     *          developerPassword2: string
     *          mysqlDatabaseName: string
     *          mysqlHostname: string
     *          mysqlPassword: string
     *          mysqlUsername: string
     *          websiteHostname: string
     *      }
     *
     * @return object
     *
     *      {
     *          succeeded: bool
     *          cbmessage: string
     *
     *              This will only be used if succeeded is false.
     *      }
     */
    static function CBAjax_verifyDatabaseUser(
        stdClass $args
    ): stdClass {

        /* Developer email address */

        $developerEmailAddress = CBModel::valueAsEmail(
            $args,
            'developerEmailAddress'
        );

        if ($developerEmailAddress === null) {
            throw new CBException(
                'The developer email address is not valid.',
                '',
                '833e8864fce5dbd4ba67e24daeeec83ce98345f2'
            );
        }


        /* Developer password 1 */

        $developerPassword1 = CBModel::valueToString(
            $args,
            'developerPassword1'
        );

        $developerPassword2 = CBModel::valueToString(
            $args,
            'developerPassword2'
        );

        $passwordIssues = CBUser::passwordIssues(
            $developerPassword1
        );

        if ($passwordIssues !== null) {
            return (object)[
                'cbmessage' => CBMessageMarkup::stringToMessage(
                    $passwordIssues
                ),
            ];
        }

        if ($developerPassword1 !== $developerPassword2) {
            return (object)[
                'cbmessage' => 'Your passwords don\'t match.',
            ];
        }


        /* Website hostname */

        $websiteHostname = trim(
            CBModel::valueToString(
                $args,
                'websiteHostname'
            )
        );

        if ($websiteHostname === '') {
            throw new CBException(
                'The website hostname is empty.',
                '',
                'd57c10eb5b3093eadf5abea2a45ee6003241d9c9'
            );
        }


        /* MySQL binary directory */

        $mysqlBinaryDirectory = trim(
            CBModel::valueToString(
                $args,
                'mysqlBinaryDirectory'
            )
        );

        if (
            $mysqlBinaryDirectory !== '' &&
            !is_dir($mysqlBinaryDirectory)
        ) {
            throw new CBException(
                'The MySQL binary directory does not exist.',
                '',
                '88f5cdc9af229e15302a055e913b60f0f90efaf8'
            );
        }


        /* MySQL hostname */

        $mysqlHostname = trim(
            CBModel::valueToString(
                $args,
                'mysqlHostname'
            )
        );

        if ($mysqlHostname === '') {
            throw new CBException(
                'The MySQL hostname is empty.',
                '',
                '41bab52757441c27b9cdefbe82652eba54ed3510'
            );
        }


        /* MySQL username */

        $mysqlUsername = trim(
            CBModel::valueToString(
                $args,
                'mysqlUsername'
            )
        );

        if ($mysqlUsername === '') {
            throw new CBException(
                'The MySQL username is empty.',
                '',
                '7ca97af9059161edc7958f57defd631d5f53de38'
            );
        }

        /* MySQL password */

        $mysqlPassword = CBModel::valueToString(
            $args,
            'mysqlPassword'
        );

        if ($mysqlPassword === '') {
            throw new CBException(
                'The MySQL password is empty.',
                '',
                '382cbf09df874ec99993337eb46bb84007f6f086'
            );
        }


        /* MySQL database name */

        $mysqlDatabaseName = trim(
            CBModel::valueToString(
                $args,
                'mysqlDatabaseName'
            )
        );

        if ($mysqlDatabaseName === '') {
            throw new CBException(
                'The MySQL database name is empty.',
                '',
                '2e46ee1efc85338390019e26cc6d0584e14487a8'
            );
        }

        /* attempt connection */

        $mysqli = new mysqli(
            $mysqlHostname,
            $mysqlUsername,
            $mysqlPassword,
            $mysqlDatabaseName,
        );

        if ($mysqli->connect_error) {
            return (object)[
                'cbmessage' => CBMessageMarkup::stringToMessage(
                    $mysqli->connect_error
                ),
            ];
        }


        /* create settings.json */

        CBSetup::createSettingsFile(
            $developerEmailAddress,
            $developerPassword1
        );


        /* create colby-configuration.php */

        $encryptionPassword = CBID::generateRandomCBID();

        $contents = <<<EOT
        <?php

        define(
            'CBSiteURL',
            'https://{$websiteHostname}'
        );

        define(
            'CBMySQLHost',
            '{$mysqlHostname}'
        );

        define(
            'CBMySQLUser',
            '{$mysqlUsername}'
        );

        define(
            'CBMySQLPassword',
            '{$mysqlPassword}'
        );

        define(
            'CBMySQLDatabase',
            '$mysqlDatabaseName'
        );

        define(
            'CBEncryptionPassword',
            '{$encryptionPassword}'
        );

        EOT;

        if ($mysqlBinaryDirectory !== '') {
            $contents .= <<<EOT

            define(
                'CBMySQLDirectory',
                '{$mysqlBinaryDirectory}'
            );

            EOT;
        }

        file_put_contents(
            cbsitedir() . '/colby-configuration.php',
            $contents
        );


        /* done */

        return (object)[
            'succeeded' => true,
        ];
    }
    /* CBAjax_verifyDatabaseUser() */



    /**
     * @return string
     */
    static function CBAjax_verifyDatabaseUser_getUserGroupClassName(
    ): string {
        if (CBSiteIsConfigured) {
            return 'CBDevelopersUserGroup';
        } else {
            return 'CBPublicUserGroup';
        }
    }
    /* CBAjax_verifyDatabaseUser_getUserGroupClassName() */



    /* -- functions -- -- -- -- -- */



    /**
     * @return void
     */
    static function bootstrap(
        string $websiteDirectory
    ): void {
        /* /index.php */

        $sourceFilepath = __DIR__ . '/index.template.data';
        $destinationFilepath = $websiteDirectory . '/index.php';

        copy(
            $sourceFilepath,
            $destinationFilepath
        );

        /* /.htaccess */

        $sourceFilepath = __DIR__ . '/htaccess.template.data';
        $destinationFilepath = $websiteDirectory . '/.htaccess';

        copy(
            $sourceFilepath,
            $destinationFilepath
        );
    }
    /* bootstrap() */



    /**
     * @return void
     */
    static function createSettingsFile(
        string $developerEmailAddress,
        string $developerPassword
    ): void {
        $settingsFilepath = (
            cbsitedir() .
            '/settings.json'
        );

        $developerPasswordHash = password_hash(
            $developerPassword,
            PASSWORD_DEFAULT
        );

        $settingsObject = (object)[
            'developerEmailAddress' => $developerEmailAddress,
            'developerPasswordHash' => $developerPasswordHash,
        ];

        file_put_contents(
            $settingsFilepath,
            json_encode(
                $settingsObject
            )
        );
    }
    /* createSettingsFile() */



    /**
     * @return void
     */
    static function renderSetupPage(): void {
        $pageSpec = (object)[
            'className' => 'CBViewPage',

            'classNameForSettings' => 'CBPageSettingsForSetup',

            'title' => 'CBSetup',

            'sections' => [
                (object)[
                    'className' => 'CBSetupView',
                ],
            ],
        ];

        CBPage::render(
            CBModel::build(
                $pageSpec
            )
        );
    }
    /* renderSetupPage() */

}
/* CBSetup */



/**
 *
 */
final class CBPageSettingsForSetup {

    /* -- CBPageSettings interfaces -- -- -- -- -- */



    /**
     * @return [string]
     */
    static function CBPageSettings_htmlElementClassNames(): array {
        return [
            'CBLightTheme',
            'CBStyleSheet'
        ];
    }
    /* CBPageSettings_htmlElementClassNames() */



    /**
     * @return [string]
     */
    static function CBPageSettings_requiredClassNames(): array {
        return [
            'CBEqualizePageSettingsPart',
            'CBResponsiveViewportPageSettingsPart',
        ];
    }
    /* CBPageSettings_requiredClassNames() */

}
/* CBPageSettingsForSetup */

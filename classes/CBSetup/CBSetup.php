<?php

final class CBSetup {

    /* -- CBAjax interfaces -- -- -- -- -- */



    /**
     * @param object $args
     *
     *      {
     *          hostname: string
     *          password: string
     *          username: string
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
        $hostname = trim(
            CBModel::valueToString(
                $args,
                'hostname'
            )
        );

        $username = trim(
            CBModel::valueToString(
                $args,
                'username'
            )
        );

        $password = CBModel::valueToString(
            $args,
            'password'
        );

        $mysqli = new mysqli(
            $hostname,
            $username,
            $password
        );

        if ($mysqli->connect_error) {
            return (object)[
                'cbmessage' => CBMessageMarkup::stringToMessage(
                    $mysqli->connect_error
                ),
            ];
        }

        return (object)[
            'succeeded' => true,
        ];
    }



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

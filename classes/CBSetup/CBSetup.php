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
    static function CBAjax_verifyDatabaseUser_getUserGroupClassName(): string {
        if (CBSiteVersionNumber === 'setup') {
            return 'CBPublicUserGroup';
        } else {
            return 'CBDevelopersUserGroup';
        }
    }



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

            'classNameForSettings' => 'CBPageSettingsForResponsivePages',

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

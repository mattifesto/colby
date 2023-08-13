<?php

final class
CB_Ajax_Update_PHPComposer
{
    // -- CBAjax interfaces



    /**
     * @return void
     */
    static function
    CBAjax_execute(
    ): void
    {
        $originalDirectory =
        getcwd();

        chdir(
            cb_document_root_directory()
        );



        try
        {
            $userInformation =
            posix_getpwuid(
                posix_getuid()
            );

            $userHomeDirectory =
            $userInformation[
                'dir'
            ];

            putenv(
                "HOME={$userHomeDirectory}"
            );

            $sitePreferencesModel =
            CBSitePreferences::model();

            $siteEnvironment =
            CBSitePreferences::getEnvironment(
                $sitePreferencesModel
            );

            if (
                $siteEnvironment === 'CBSitePreferences_environment_development'
            ) {
                $commands =
                [
                    'composer update',
                ];
            }

            else
            {
                $commands =
                [
                    'composer install',
                ];
            }

            foreach (
                $commands as
                $command
            ) {
                $output = [];
                $exitCode = null;

                CBExec::exec(
                    $command,
                    $output,
                    $exitCode
                );

                if (
                    $exitCode !== 0
                ) {
                    $outputAsText =
                    implode(
                        "\n",
                        $output
                    );

                    throw new CBExceptionWithValue(
                        'installing php composer dependencies failed',
                        $outputAsText,
                        'd8d71ef9485c9c9df3ca0d724f76fdc448ed3aca'
                    );
                }
            }
        }

        finally
        {
            chdir(
                $originalDirectory
            );
        }
    }
    // CBAjax_execute()



    /**
     * @param CBID callingUserModelCBID
     *
     * @return bool
     */
    static function
    CBAjax_userModelCBIDCanExecute(
        ?string $callingUserModelCBID = null
    ): bool
    {
        if (
            $callingUserModelCBID ===
            null
        ) {
            return false;
        }

        $userIsAnAdministrator =
        CBUserGroup::userIsMemberOfUserGroup(
            $callingUserModelCBID,
            'CBDevelopersUserGroup'
        );

        return $userIsAnAdministrator;
    }
    // CBAjax_userModelCBIDCanExecute()

}

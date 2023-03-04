<?php

final class
CB_Ajax_InstallPHPComposerDependencies
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

        $documentRootDirectory =
        cb_document_root_directory();

        chdir(
            $documentRootDirectory
        );

        try
        {
            putenv(
                "COMPOSER_HOME={$documentRootDirectory}"
            );

            $command =
            'composer install';

            $output =
            [];

            $exitCode;

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
                    'composer install failed',
                    $outputAsText,
                    'd8d71ef9485c9c9df3ca0d724f76fdc448ed3aca'
                );
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
            'CBAdministratorsUserGroup'
        );

        return $userIsAnAdministrator;
    }
    // CBAjax_userModelCBIDCanExecute()

}

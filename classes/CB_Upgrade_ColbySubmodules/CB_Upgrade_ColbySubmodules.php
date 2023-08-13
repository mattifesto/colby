<?php

final class
CB_Upgrade_ColbySubmodules
{
    // -- CBInstall interfaces



    /**
     * @return void
     */
    static function
    CBInstall_install(
    ): void
    {
        /**
         * The operations of this function should only be run if Colby is
         * currently installed as a PHP Composer library.
         */
        if (
            cb_php_composer_colby_library_is_installed() !== true
        ) {
            return;
        }

        $deprecatedColbyLibraryDirectory =
        cb_document_root_directory() . '/colby';

        CB_Upgrade_ColbySubmodules::deleteDeprecatedSubmoduleDirectory(
            $deprecatedColbyLibraryDirectory
        );

        $deprecatedSwiftMailerDirectory =
        cb_document_root_directory() . '/swiftmailer';

        CB_Upgrade_ColbySubmodules::deleteDeprecatedSubmoduleDirectory(
            $deprecatedSwiftMailerDirectory
        );
    }
    // CBInstall_install()



     // -- functions


    /**
     * @return void
     */
    private static function
    deleteDeprecatedSubmoduleDirectory(
        string $absoluteDirectoryPath
    ): void
    {
        $directoryNoLongerExists =
        !is_dir($absoluteDirectoryPath);

        if (
            $directoryNoLongerExists
        ) {
            return;
        }

        $directoryStillContainsAnActiveGitSubmodule =
        CBGit::absoluteDirectoryPathIsASubmodule(
            $absoluteDirectoryPath
        );

        if (
            $directoryStillContainsAnActiveGitSubmodule
        ) {
            return;
        }

        $command = "rm -rf {$absoluteDirectoryPath}";
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

            $outputAsCBMessage =
            CBMessageMarkup::stringToMessage(
                $outputAsText
            );


            $exceptionCBMessage =
            <<<EOT

                --- pre\n{$outputAsCBMessage}
                ---

            EOT;

            throw new CBException(
                "The following command failed: {$command}.",
                $exceptionCBMessage,
                'be6d629248be53822da4a4766393a52430e7bbb9'
            );
        }
    }
    // deleteDeprecatedSubmoduleDirectory()
}

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
        if (
            cb_php_composer_colby_library_is_installed() !== true
        ) {
            /**
             * The operations of this function should only be run if Colby is
             * currently installed as a PHP Composer library.
             */

            return;
        }

        CB_Upgrade_ColbySubmodules::cleanUpSubmodule(
            'colby'
        );

        CB_Upgrade_ColbySubmodules::cleanUpSubmodule(
            'swiftmailer'
        );
    }
    // CBInstall_install()



    // -- functions



    /**
     * @param string $documentRootRelativeSubmoduleDirectory
     *
     * @return void
     */
    private static function
    cleanUpSubmodule(
        string $documentRootRelativeSubmoduleDirectoryArgument
    ): void
    {
        if (
            !in_array(
                $documentRootRelativeSubmoduleDirectoryArgument,
                ['colby', 'swiftmailer']
            )
        ) {
            $message =
            CBConvert::stringToCleanLine(<<<EOT

                The string "{$documentRootRelativeSubmoduleDirectoryArgument}"
                is not a valid argument value.

            EOT);

            throw new CBExceptionWithValue(
                $message,
                $documentRootRelativeSubmoduleDirectoryArgument,
                'bad7c26852c2806aa9fa088a57708aae8b58c040'
            );
        }

        $arrayOfDocumentRootRelativeSubmoduleDirectories =
        CBGit::submodules();

        if (
            in_array(
                $documentRootRelativeSubmoduleDirectoryArgument,
                $arrayOfDocumentRootRelativeSubmoduleDirectories
            )
        ) {
            /**
             * Don't clean up the submodule if it's still active.
             */

            return;
        }

        $absoluteDirectoryPath =
        cb_document_root_directory() .
        "/{$documentRootRelativeSubmoduleDirectoryArgument}";



        $pwd =
        getcwd();

        chdir(
            cb_document_root_directory()
        );

        try
        {
            /**
             * Step 1 of 3 (steps can be run in any order)
             * Remove submodule folder.
             */

            $command = "rm -rf {$absoluteDirectoryPath} 2>&1";
            $arrayOfOutputLines = [];
            $exitCode = null;

            CBExec::exec(
                $command,
                $arrayOfOutputLines,
                $exitCode
            );

            CB_Upgrade_ColbySubmodules::checkForAndReportError(
                $command,
                $arrayOfOutputLines,
                $exitCode
            );



            /**
             * Step 2 of 3 (steps can be run in any order)
             * Remove submodule folder.
             */

            $absoluteGitModuleDirectory =
            cb_document_root_directory() .
            "/.git/modules/{$documentRootRelativeSubmoduleDirectoryArgument}";

            $command = "rm -rf {$absoluteGitModuleDirectory} 2>&1";
            $arrayOfOutputLines = [];
            $exitCode = null;

            CBExec::exec(
                $command,
                $arrayOfOutputLines,
                $exitCode
            );

            CB_Upgrade_ColbySubmodules::checkForAndReportError(
                $command,
                $arrayOfOutputLines,
                $exitCode
            );



            /**
             * Step 3 of 3 (steps can be run in any order)
             * Remove submodule folder.
             */

            $command =
            CBConvert::stringToCleanLine(<<<EOT

                git config --get
                submodule.{$documentRootRelativeSubmoduleDirectoryArgument}.url
                2>&1

            EOT);

            $arrayOfOutputLines = [];
            $exitCode = null;

            CBExec::exec(
                $command,
                $arrayOfOutputLines,
                $exitCode
            );

            if (
                $exitCode === 1
            ) {
                /**
                 * An exit code of 1 means the key was not found which means we have
                 * no more work to do.
                 */

                return;
            }

            CB_Upgrade_ColbySubmodules::checkForAndReportError(
                $command,
                $arrayOfOutputLines,
                $exitCode
            );



            $command =
            CBConvert::stringToCleanLine(<<<EOT

                git config --remove-section
                submodule.{$documentRootRelativeSubmoduleDirectoryArgument}

            EOT);

            $arrayOfOutputLines = [];
            $exitCode = null;

            CBExec::exec(
                $command,
                $arrayOfOutputLines,
                $exitCode
            );

            CB_Upgrade_ColbySubmodules::checkForAndReportError(
                $command,
                $arrayOfOutputLines,
                $exitCode
            );
        }

        finally
        {
            chdir(
                $pwd
            );
        }
    }
    // cleanUpSubmodule()



    /**
     *
     */
    private static function
    checkForAndReportError(
        string $command,
        array $arrayOfOutputLines,
        int $exitCode
    ): void
    {
        if (
            $exitCode === 0
        ) {
            return;
        }

        $outputAsText =
        implode(
            "\n",
            $arrayOfOutputLines
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
    // reportError()
}

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

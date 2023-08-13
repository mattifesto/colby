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

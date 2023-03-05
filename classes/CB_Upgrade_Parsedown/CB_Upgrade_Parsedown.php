<?php

final class
CB_Upgrade_Parsedown
{
    // -- CBInstall interfaces



    /**
     * @return void
     */
    static function
    CBInstall_install(
    ): void
    {
        $deprecatedParsedownSubmoduleDirectory =
        cbsysdir() .
        '/classes/Parsedown';

        $theDeprecatedParsedownSubmoduleDirectoryExists =
        is_dir(
            $deprecatedParsedownSubmoduleDirectory
        );

        if (
            $theDeprecatedParsedownSubmoduleDirectoryExists
        ) {
            $command =
            "rm -rf {$deprecatedParsedownSubmoduleDirectory}";

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
                    'Unable to delete Colby\'s deprecated Parsedown directory.',
                    $exceptionCBMessage,
                    '0d73a39e92962c356be48494b2c8a85acbba836f'
                );
            }
        }
    }
    // CBInstall_install()

}

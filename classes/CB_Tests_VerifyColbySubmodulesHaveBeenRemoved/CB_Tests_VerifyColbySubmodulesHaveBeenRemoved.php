<?php

final class
CB_Tests_VerifyColbySubmodulesHaveBeenRemoved
{
    // -- CBTest interfaces



    /**
     * @return [object]
     */
    static function
    CBTest_getTests(
    ): array {
        return [
            (object)[
                'name' => 'run',
                'type' => 'server',
            ],
        ];
    }
    /* CBTest_getTests() */



    /* -- tests  -- -- -- -- -- */



    /**
     * @return object
     */
    static function
    run(
    ): void
    {
        if (
            cb_php_composer_colby_library_is_installed() !== true
        ) {
            /**
             * This test doesn't need to be run unless the Colby PHP Composer
             * library has been installed.
             */

            return;
        }

        $deprecatedColbyLibraryDirectory =
        cb_document_root_directory() . '/colby';

        CB_Tests_VerifyColbySubmodulesHaveBeenRemoved::testPotentialSubmoduleDirectory(
            $deprecatedColbyLibraryDirectory
        );

        $deprecatedSwiftMailerDirectory =
        cb_document_root_directory() . '/swiftmailer';

        CB_Tests_VerifyColbySubmodulesHaveBeenRemoved::testPotentialSubmoduleDirectory(
            $deprecatedSwiftMailerDirectory
        );
    }
    // run()



    // -- functions



    /**
     * @param string $absoluteDirectoryPathArgument
     *
     * @return void
     */
    private static function
    testPotentialSubmoduleDirectory(
        string $absoluteDirectoryPathArgument
    ): void
    {
        if (
            CBGit::absoluteDirectoryPathIsASubmodule(
                $absoluteDirectoryPathArgument
            )
        ) {
            $message =
            CBConvert::stringToCleanLine(<<<EOT

                The {$absoluteDirectoryPathArgument} is a submodule even
                though the PHP Composer Colby library has been installed. In the
                development environment run `git rm
                {$absoluteDirectoryPathArgument}`.

            EOT);

            throw new Exception($message);
        }

        if (
            is_dir($absoluteDirectoryPathArgument)
        ) {
            $message =
            CBConvert::stringToCleanLine(<<<EOT

                The {$absoluteDirectoryPathArgument} still exists even though
                the Colby PHP Composer library has been installed. Run update to
                remove it.

            EOT);

            throw new Exception($message);
        }
    }
    // testPotentialSubmoduleDirectory()
}

<?php

final class
CB_Tests_Install
{
    // -- CBTest interfaces



    /**
     * @return [object]
     */
    static function
    CBTest_getTests(
    ): array
    {
        $tests =
        [
            (object)
            [
                'type' =>
                'server',

                'name' =>
                'verify_gitignore_file',
            ],
            (object)
            [
                'type' =>
                'server',

                'name' =>
                'verify_htaccess_file',
            ],
        ];

        return $tests;
    }
    // CBTest_getTests()



    // -- tests



    /**
     * @return void
     */
    static function
    verify_gitignore_file(
    ): void
    {
        $websiteGitignoreAbsoluteFilename =
        cbsitedir() .
        '/.gitignore';

        $websiteGitignoreSHA1Hash =
        sha1_file(
            $websiteGitignoreAbsoluteFilename
        );

        $setupGitignoreAbsoluteFilename =
        cbsysdir() .
        '/setup/gitignore.template.data';

        $setupGitignoreSHA1Hash =
        sha1_file(
            $setupGitignoreAbsoluteFilename
        );

        if (
            $websiteGitignoreSHA1Hash !==
            $setupGitignoreSHA1Hash
        ) {
            $message = <<<EOT

                The file "${websiteGitignoreAbsoluteFilename}" does not match
                the file "${setupGitignoreAbsoluteFilename}".

            EOT;

            throw new Exception(
                CBConvert::stringToCleanLine(
                    $message
                )
            );
        }
    }
    // verify_gitignore_file()



    /**
     * @return void
     */
    static function
    verify_htaccess_file(
    ): void
    {
        $website_htaccess_absolute_filename =
        cbsitedir() .
        '/.htaccess';

        $website_htaccess_SHA1_hash =
        sha1_file(
            $website_htaccess_absolute_filename
        );

        $setup_htaccess_absolute_filename =
        cbsysdir() .
        '/setup/htaccess.template.data';

        $setup_htaccess_SHA1_hash =
        sha1_file(
            $setup_htaccess_absolute_filename
        );

        if (
            $website_htaccess_SHA1_hash !==
            $setup_htaccess_SHA1_hash
        ) {
            $message = <<<EOT

                The file "${website_htaccess_absolute_filename}" does not match
                the file "${setup_htaccess_absolute_filename}".

            EOT;

            throw new Exception(
                CBConvert::stringToCleanLine(
                    $message
                )
            );
        }
    }
    // verify_htaccess_file()

}

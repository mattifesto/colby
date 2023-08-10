<?php

final class
CB_Tests_VerifyIndex
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
        $absoluteIndexTemplateFilepath =
        cbsysdir() . '/setup/template-index.php';

        $absoluteIndexFilepath =
        cb_document_root_directory() . '/index.php';

        $indexTemplateContents =
        file_get_contents(
            $absoluteIndexTemplateFilepath
        );

        $indexContents =
        file_get_contents(
            $absoluteIndexFilepath
        );

        if (
            $indexContents !== $indexTemplateContents
        ) {
            $message =
            CBConvert::stringToCleanLine(<<<EOT

                The file `{$absoluteIndexFilepath}` does not match the file
                `{$absoluteIndexTemplateFilepath}`. The file
                `{$absoluteIndexFilepath}` should be updated.

            EOT);

            throw new CBException(
                $message,
                '',
                '76486f37fe7637f93aaae782a40aa1b8b414e78e'
            );
        }
    }
    // run()
}

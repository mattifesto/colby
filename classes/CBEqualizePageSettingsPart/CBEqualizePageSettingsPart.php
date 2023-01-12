<?php

final class
CBEqualizePageSettingsPart
{
    // -- CBCodeAdmin interfaces



    /**
     * @return [object]
     */
    static function
    CBCodeAdmin_searches(
    ): array
    {
        $searches =
        [];



        // CBBackgroundColor

        $codeSearchSpec =
        CBModel::createSpec(
            'CBCodeSearch',
            '47575ebc7532925602cc4cd2e0bf1b705e73bc0c'
        );

        CBCodeSearch::setAckArguments(
            $codeSearchSpec,
            '--ignore-file=match:CBEqualizePageSettingsPart\.'
        );

        CBCodeSearch::setNoticeVersion(
            $codeSearchSpec,
            '2021_09_02_1655782594'
        );

        CBCodeSearch::setWarningVersion(
            $codeSearchSpec,
            '2022_06_21_1655783579'
        );

        CBCodeSearch::setErrorVersion(
            $codeSearchSpec,
            '2023_01_12_1673533018'
        );

        $codeSearchSpec->cbmessage =
        <<<EOT

            Use CBBackgroundColor1

        EOT;

        $codeSearchSpec->regex =
        '\bCBBackgroundColor\b';

        $codeSearchSpec->severity =
        3;

        $codeSearchSpec->title =
        'CBBackgroundColor';

        array_push(
            $searches,
            $codeSearchSpec
        );



        // CBTextColor

        $codeSearchSpec =
        CBModel::createSpec(
            'CBCodeSearch',
            '37194ab3f05b8295cd76f12da1b65995498abcfe'
        );

        CBCodeSearch::setNoticeVersion(
            $codeSearchSpec,
            '2022_06_21_1655783117'
        );

        CBCodeSearch::setWarningVersion(
            $codeSearchSpec,
            '2022_06_21_1655783609'
        );

        $codeSearchSpec->cbmessage =
        <<<EOT

            Use CBTextColor1

        EOT;

        $codeSearchSpec->regex =
        '\bCBTextColor\b';

        $codeSearchSpec->severity =
        4;

        $codeSearchSpec->title =
        'CBTextColor';

        array_push(
            $searches,
            $codeSearchSpec
        );



        return $searches;
    }
    /* CBCodeAdmin_searches() */



    /* -- CBPageSettings interfaces -- */



    /**
     * @NOTE 2022_06_20
     *
     *      Implementing this function instead of CBHTMLOutput_CSSURLs() ensures
     *      that the CSS file is included in the head element before the files
     *      included because thare returned by CBHTMLOutput_CSSURLs().
     *
     *      I'm not 100% sure this is completely necesary. Document discovered
     *      facts here.
     *
     * @return void
     */
    static function
    CBPageSettings_renderHeadElementHTML(
    ): void
    {
        $CSSURL =
        CBLibrary::buildLibraryClassFilePath(
            __CLASS__,
            '2022_10_15_1665851309',
            'css',
            cbsysurl()
        );

        ?>
        <link rel="stylesheet" href="<?= $CSSURL ?>">
        <?php
    }
    /* CBPageSettings_renderHeadElementHTML() */

}

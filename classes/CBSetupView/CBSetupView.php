<?php

/**
 * @deprecated 2022_01_29
 *
 *      This class has been effectively removed.
 */
final class
CBSetupView
{
    // -- CBCodeAdmin interfaces



    /**
     * @return [object]
     */
    static function
    CBCodeAdmin_searches(
    ): stdClass
    {
        $codeSearchSpec =
        CBModel::createSpec(
            'CBCodeSearch',
            '1b70fd5c75bcaa7c0a0339bd9d94c0ae61ec01d7'
        );

        CBCodeSearch::setErrorVersion(
            $codeSearchSpec,
            '2022_06_20_1655739482'
        );

        CBCodeSearch::setNoticeVersion(
            $codeSearchSpec,
            '2022_06_03_1654267756'
        );

        CBCodeSearch::setWarningVersion(
            $codeSearchSpec,
            '2022_06_20_1655739481'
        );

        $codeSearchSpec->args =
        '--ignore-file=is:CBSetupView.php';

        $codeSearchSpec->cbmessage =
        <<<EOT

            Websites are no longer set up using this method, they are
            set up fully using the cbt command in terminal

        EOT;

        $codeSearchSpec->regex =
        '\bCBSetupView\b';

        $codeSearchSpec->severity =
        3;

        $codeSearchSpec->title =
        'CBSetupView';

        return $codeSearchSpec;
    }
    /* CBCodeAdmin_searches() */

}

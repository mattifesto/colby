<?php

/**
 * @deprecated 2022_01_29
 *
 *      This class has been effectively removed.
 */
final class
CBSetup
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
            '70870720e297d232c57a68f8871f06514d691708'
        );

        CBCodeSearch::setErrorVersion(
            $codeSearchSpec,
            '2022_06_20_1655692698'
        );

        CBCodeSearch::setNoticeVersion(
            $codeSearchSpec,
            '2022_06_03_1654267262'
        );

        CBCodeSearch::setWarningVersion(
            $codeSearchSpec,
            '2022_06_20_1655692697'
        );

        $codeSearchSpec->args =
        '--ignore-file=is:CBSetup.php';

        $codeSearchSpec->cbmessage =
        <<<EOT

            Websites are no longer set up using this method, they are set up
            fully using the cbt command in terminal

        EOT;

        $codeSearchSpec->regex =
        '\bCBSetup\b';

        $codeSearchSpec->severity =
        3;

        $codeSearchSpec->title =
        'CBSetup';

        return $codeSearchSpec;
    }
    /* CBCodeAdmin_searches() */

}

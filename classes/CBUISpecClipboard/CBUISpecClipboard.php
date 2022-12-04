<?php

final class
CBUISpecClipboard
{
    // -- CBCodeAdmin interfaces



    /**
     * @return [object]
     */
    static function
    CB_CBAdmin_Code_getDeprecatedCodeSearches(
    ): array
    {
        $searches =
        [];



        // CBUISpecClipboard.specs

        $codeSearchSpec =
        CBModel::createSpec(
            'CBCodeSearch',
            '2b59f53f576404075b95249cff87477144b3041c'
        );

        CBCodeSearch::setNoticeVersion(
            $codeSearchSpec,
            '2022_12_04_1670170445'
        );

        CBCodeSearch::setWarningVersion(
            $codeSearchSpec,
            '2022_12_04_1670170446'
        );

        CBCodeSearch::setErrorVersion(
            $codeSearchSpec,
            '2022_12_04_1670170447'
        );

        CBCodeSearch::setCBMessage(
            $codeSearchSpec,
            <<<EOT

                Use CBUISpecClipboard.getSpecs() or
                CBUISpecClipboard.setSpecs().

            EOT
        );

        CBCodeSearch::setFileType(
            $codeSearchSpec,
            'js'
        );

        $codeSearchSpec->regex =
        '\bCBUISpecClipboard\.specs\b';

        $codeSearchSpec->severity =
        3;

        $codeSearchSpec->title =
        'CBUISpecClipboard.specs';

        array_push(
            $searches,
            $codeSearchSpec
        );



        return $searches;
    }
    /* CBCodeAdmin_searches() */



    // -- CBHTMLOutput interfaces



    /**
     * @return [string]
     */
    static function
    CBHTMLOutput_JavaScriptURLs(
    ): array
    {
        $arrayOfJavaScriptURLs =
        [
            Colby::flexpath(
                __CLASS__,
                'v361.js',
                cbsysurl()
            ),
        ];

        return $arrayOfJavaScriptURLs;
    }
    // CBHTMLOutput_JavaScriptURLs()



    /**
     * @return [string]
     */
    static function
    CBHTMLOutput_requiredClassNames(
    ): array
    {
        $arrayOfRequiredClassNames =
        [
            'CBConvert',
        ];

        return $arrayOfRequiredClassNames;
    }
    // CBHTMLOutput_requiredClassNames()

}

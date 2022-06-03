<?php

final class
CBUISpecSaver
{
    /* -- CBHTMLOutput interfaces -- */



    /**
     * @return [string]
     */
    static function
    CBHTMLOutput_JavaScriptURLs(
    ): array
    {
        return
        [
            Colby::flexpath(
                __CLASS__,
                'v675.2.js',
                scliburl()
            ),
        ];
    }
    /* CBHTMLOutput_JavaScriptURLs() */



    /**
     * @return [string]
     */
    static function
    CBHTMLOutput_requiredClassNames(
    ): array
    {
        return
        [
            'CBAjax',
            'CBErrorHandler',
            'CBException',
            'CBModel',
            'CBUIPanel',
        ];
    }
    /* CBHTMLOutput_requiredClassNames() */



    // -- CB_CBAdmin_Code interfaces



    /**
     * @return [object]
     */
    static function
    CBCodeAdmin_searches(
    ): array
    {
        return
        [
            (object)[
                'args' =>
                '--ignore-file=match:CBUISpecSaver.js ' .
                '--ignore-file=match:CBUISpecSaver.php ',

                'regex' =>
                'CBUISpecSaver',

                'severity' =>
                4,

                'title' =>
                'CBUISpecSaver',

                'cbmessage' =>
                <<<EOT

                    Use CBSpecSaver

                EOT,

                'noticeStartDate' =>
                '2020/12/25',

                'noticeVersion' =>
                675,

                'CBCodeSearch_CBID' =>
                'e4974905aa83c5256a7b7352b9509676af4013f3',

                'CBCodeSearch_warningVersion_property' =>
                '2022_06_03_1654294313'
            ],
        ];
    }
    // CBCodeAdmin_searches()

}

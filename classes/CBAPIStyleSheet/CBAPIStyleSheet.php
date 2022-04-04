<?php

/**
 * @deprecated 2022_04_03
 *
 *      This class doesn't appear to be used anywhere and it's CSS class names
 *      are not unique strings.
 */
final class
CBAPIStyleSheet
{
    // CBCodeAdmin interfaces



    /**
     * @return [object]
     */
    static function
    CBCodeAdmin_searches(
    ): array
    {
        return [
            (object)
            [
                'args' =>
                '--ignore-file=match:CBAPIStyleSheet.php ' .
                '--ignore-file=match:CBAPIStyleSheet.css ',

                'CBCodeSearch_CBID' =>
                '71acb6764a6288edc4f9d9b547dd1cb5daa4ad1b',

                'cbmessage' =>
                <<<EOT

                    While doing maintenance it was found that this class doesn't
                    appear to be used. It is older and doesn't follow unique CSS
                    class name standards so it should not be used.

                EOT,

                'regex' =>
                '\bCBAPIStyleSheet\b',

                'severity' =>
                5,

                'title' =>
                'CBAPIStyleSheet',

                'noticeStartDate' =>
                '2022/04/03',

                'noticeVersion' =>
                675,
            ],
        ];
    }
    // CBCodeAdmin_searches()



    /* -- CBHTMLOutput interfaces -- */



    /**
     * @return [string]
     */
    static function
    CBHTMLOutput_CSSURLs(
    ): array
    {
        return
        [
            Colby::flexpath(
                __CLASS__,
                'v675.69.css',
                cbsysurl()
            )
        ];
    }
    // CBHTMLOutput_CSSURLs()

}

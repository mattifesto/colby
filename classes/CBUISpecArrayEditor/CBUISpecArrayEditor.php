<?php

final class
CBUISpecArrayEditor
{
    // -- CBHTMLOutput interfaces



    /**
     * @return [string]
     */
    static function CBHTMLOutput_CSSURLs() {
        return [
            Colby::flexpath(__CLASS__, 'v601.css', cbsysurl()),
        ];
    }



    /**
     * @return [string]
     */
    static function
    CBHTMLOutput_JavaScriptURLs(
    ): array
    {
        $arrayOfJavaScriptURLs =
        [
            CBLibrary::buildLibraryClassFilePath(
                __CLASS__,
                '2022_12_02_1669954497',
                'js',
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
    ) {
        return [
            'CB_UI',
            'CBErrorHandler',
            'CBException',
            'CBUI',
            'CBUICommandPart',
            'CBUINavigationView',
            'CBUIPanel',
            'CBUISelectableItem',
            'CBUISelectableItemContainer',
            'CBUISelector',
            'CBUISpec',
            'CBUISpecClipboard',
            'CBUISpecEditor',
            'CBUIThumbnailPart',
            'CBUITitleAndDescriptionPart',
        ];
    }
    /* CBHTMLOutput_requiredClassNames() */

}
/* CBUISpecArrayEditor */

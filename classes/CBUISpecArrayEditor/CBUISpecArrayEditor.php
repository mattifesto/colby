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
            Colby::flexpath(
                __CLASS__,
                'v675.51.js',
                cbsysurl()
            )
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

<?php

final class CBArtworkViewEditor {

    /* -- CBHTMLOutput interfaces -- -- -- -- -- */



    /**
     * @return [string]
     */
    static function
    CBHTMLOutput_JavaScriptURLs(
    ): array {
        return [
            Colby::flexpath(
                __CLASS__,
                'v675.4.js',
                cbsysurl()
            ),
        ];
    }
    /* CBHTMLOutput_JavaScriptURLs() */



    /**
     * @return [string]
     */
    static function CBHTMLOutput_requiredClassNames(): array {
        return [
            'CBAjax',
            'CBImage',
            'CBMessageMarkup',
            'CBModel',
            'CBUI',
            'CBUIBooleanSwitchPart',
            'CBUIImageChooser',
            'CBUIPanel',
            'CBUISelector',
            'CBUIStringEditor2',
        ];
    }
    /* CBHTMLOutput_requiredClassNames() */

}
/* CBArtworkViewEditor */

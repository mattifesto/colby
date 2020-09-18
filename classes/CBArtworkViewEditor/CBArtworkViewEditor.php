<?php

final class CBArtworkViewEditor {

    /* -- CBHTMLOutput interfaces -- -- -- -- -- */



    /**
     * @return [string]
     */
    static function CBHTMLOutput_JavaScriptURLs(): array {
        return [
            Colby::flexpath(__CLASS__, 'v640.js', cbsysurl()),
        ];
    }



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
            'CBUIStringEditor',
        ];
    }
    /* CBHTMLOutput_requiredClassNames() */

}
/* CBArtworkViewEditor */

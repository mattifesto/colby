<?php

final class CBArtworkViewEditor {

    /* -- CBHTMLOutput interfaces -- -- -- -- -- */



    /**
     * @return [string]
     */
    static function CBHTMLOutput_JavaScriptURLs(): array {
        return [
            Colby::flexpath(__CLASS__, 'v609.js', cbsysurl()),
        ];
    }



    /**
     * @return [string]
     */
    static function CBHTMLOutput_requiredClassNames(): array {
        return [
            'CBImage',
            'CBMessageMarkup',
            'CBModel',
            'CBUI',
            'CBUIBooleanSwitchPart',
            'CBUIImageChooser',
            'CBUIPanel',
            'CBUISelector',
            'CBUIStringEditor',
            'Colby',
        ];
    }
    /* CBHTMLOutput_requiredClassNames() */

}
/* CBArtworkViewEditor */

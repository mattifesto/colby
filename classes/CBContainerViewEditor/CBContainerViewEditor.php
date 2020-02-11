<?php

final class CBContainerViewEditor {

    /* -- CBHTMLOutput interfaces -- -- -- -- -- */



    /**
     * @return [string]
     */
    static function CBHTMLOutput_JavaScriptURLs(): array {
        return [
            Colby::flexpath(__CLASS__, 'v572.js', cbsysurl()),
        ];
    }



    /**
     * @return [[<name>, <value>]]
     */
    static function CBHTMLOutput_JavaScriptVariables(): array {
        return [
            [
                'CBContainerViewEditor_addableClassNames',
                CBPagesPreferences::classNamesForAddableViews(),
            ]
        ];
    }
    /* CBHTMLOutput_JavaScriptVariables() */



    /**
     * @return [string]
     */
    static function CBHTMLOutput_requiredClassNames(): array {
        return [
            'CBImage',
            'CBModel',
            'CBUI',
            'CBUIImageChooser',
            'CBUISelector',
            'CBUISpec',
            'CBUISpecArrayEditor',
            'CBUIStringEditor',
            'Colby',
        ];
    }
    /* CBHTMLOutput_requiredClassNames() */

}
/* CBContainerViewEditor */

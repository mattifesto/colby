<?php


/**
 * This class was created to replace the CBUIStringEditor and
 * CB_UI_StringEditor2 classes with a class whose functions are fully distinct
 * and searchable.
 *
 * To accomplish this CB_UI_StringEditor.create() returns an object the only has
 * function properties with distinct names. The object has no value properties
 * of any kind because setting or getting an unsupported value property will not
 * report any error or be noticeable outside of testing. With value properties,
 * typos are not easily found.
 *
 * This class is an early attempt to create classes that only have distinct
 * searchable class + function names so that deprecation of all functionality is
 * fully possible with a simple regular expression search and no need to analyze
 * any code.
 */
final class
CB_UI_StringEditor {

    /* -- CBHTMLOutput interfaces -- -- -- -- -- */



    /**
     * @return [string]
     */
    static function
    CBHTMLOutput_CSSURLs(
    ) {
        return [
            Colby::flexpath(
                __CLASS__,
                'v675.53.css',
                cbsysurl()
            ),
        ];
    }
    /* CBHTMLOutput_CSSURLs() */



    /**
     * @return [string]
     */
    static function
    CBHTMLOutput_JavaScriptURLs(
    ): array {
        return [
            Colby::flexpath(
                __CLASS__,
                'v675.48.js',
                cbsysurl()
            ),
        ];
    }
    /* CBHTMLOutput_JavaScriptURLs() */



    /**
     * @return [string]
     */
    static function
    CBHTMLOutput_requiredClassNames(
    ): array {
        return [
            'CB_UI',
            'CBConvert',
            'CBException',
            'CBID',
            'CBModel',
            'CBUI',
        ];
    }
    /* CBHTMLOutput_requiredClassNames() */

}

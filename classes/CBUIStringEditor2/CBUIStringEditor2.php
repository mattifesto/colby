<?php


/**
 * This class was created to replace the CBUIStringEditor class with a class
 * whose functions are fully distinct and searchable.
 *
 * To accomplish this CBUIStringEditor2.create() returns an object the only has
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
final class CBUIStringEditor2 {

    /* -- CBHTMLOutput interfaces -- -- -- -- -- */



    /**
     * @return [string]
     */
    static function
    CBHTMLOutput_CSSURLs() {
        return [
            Colby::flexpath(
                __CLASS__,
                'v675.7.css',
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
                'v675.14.js',
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
            'CBConvert',
            'CBException',
            'CBID',
            'CBModel',
            'CBUI',
        ];
    }
    /* CBHTMLOutput_requiredClassNames() */

}

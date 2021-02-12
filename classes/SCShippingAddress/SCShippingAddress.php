<?php

/**
 * @NOTE 2021_02_11
 *
 *      This class was created on this day to organize and normalize the
 *      management of the shipping address informaton that is stored in local
 *      storage.
 *
 *      Shipping addresses will at some point be stored for users on the server
 *      so this may turn into an official model.
 */
final class SCShippingAddress {

    /* -- CBHTMLOutput interfaces -- */



    /**
     * @return [string]
     */
    static function
    CBHTMLOutput_JavaScriptURLs(
    ): array {
        return [
            Colby::flexpath(
                __CLASS__,
                'v675.13.js',
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
            'CBModel',
        ];
    }
    /* CBHTMLOutput_requiredClassNames() */

}

<?php

final class CBAjaxResponse {
    // -- CBHTMLOutput interfaces



    /**
     * @return [string]
     */
    static function
    CBHTMLOutput_JavaScriptURLs(
    ): array
    {
        $javaScriptURLs =
        [
            CBLibrary::buildLibraryClassFilePath(
                __CLASS__,
                '2023_03_04_1677967513',
                'js',
                cbsysurl()
            ),
        ];

        return $javaScriptURLs;
    }
    // CBHTMLOutput_JavaScriptURLs()



    /**
     * @return [string]
     */
    static function
    CBHTMLOutput_requiredClassNames(
    ): array
    {
        $requiredClassNames =
        [
            'CBModel',
        ];

        return $requiredClassNames;
    }
    /* CBHTMLOutput_requiredClassNames() */



    /* -- CBModel interfaces -- */



    /**
     * @param object $ajaxResponseSpec
     *
     * @return object
     *
     *      {
     *          message: string
     *
     *              This property will only be set if there is an issue with the
     *              Ajax function call.
     *
     *          sourceCBID: CBID|null
     *
     *              developers only
     *
     *          stackTrace: string
     *
     *              developers only
     *
     *          userMustLogIn: bool
     *
     *              This will be set to true if the user group class name is not
     *              CBPublicUserGroup and the user is not currently logged in.
     *
     *          value: mixed
     *
     *              This will be set to the value returned by the CBAjax
     *              function if the CBAjax interfaces are implemented, the user
     *              has permission to call the Ajax function, and nothing goes
     *              wrong.
     *
     *          wasSuccessful: bool
     *
     *              This will be set to true only if the call to the Ajax
     *              function completes with no issues. Otherwise it will be
     *              false. It is an indicator of whether the Ajax function call
     *              was successful not an indicator of whether the resulting
     *              value is positive or negative.
     *      }
     */
    static function
    CBModel_build(
        object $ajaxResponseSpec
    ): stdClass {
        return (object)[
            'message' => CBModel::valueToString(
                $ajaxResponseSpec,
                'message'
            ),

            'sourceCBID' => CBModel::valueAsCBID(
                $ajaxResponseSpec,
                'sourceCBID'
            ),

            'stackTrace' => CBModel::valueToString(
                $ajaxResponseSpec,
                'stackTrace'
            ),

            'userMustLogIn' => CBModel::valueToBool(
                $ajaxResponseSpec,
                'userMustLogIn'
            ),

            'value' => CBModel::value(
                $ajaxResponseSpec,
                'value'
            ),

            'wasSuccessful' => CBModel::valueToBool(
                $ajaxResponseSpec,
                'wasSuccessful'
            ),
        ];
    }
    /* CBModel_build() */

}

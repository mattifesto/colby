<?php

final class
CB_AjaxRequest {

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
                'v675.54.js',
                cbsysurl()
            ),
        ];
    }
    /* CBHTMLOutput_JavaScriptURLs() */



    /* -- accessors -- */



    /**
     * @param object $ajaxRequestModel
     *
     * @return object|null
     */
    static function
    getExecutorArguments(
        $ajaxRequestModel
    ): ?stdClass {
        return CBModel::valueAsObject(
            $ajaxRequestModel,
            'CB_AjaxRequest_executorArguments_property'
        );
    }
    /* getExecutorArguments() */



    /**
     * @param object $ajaxRequestModel
     *
     * @return string|null
     */
    static function
    getExecutorClassName(
        $ajaxRequestModel
    ): ?string {
        return CBModel::valueAsName(
            $ajaxRequestModel,
            'CB_AjaxRequest_executorClassName_property'
        );
    }
    /* getExecutorClassName() */



    /**
     * @param object $ajaxRequestModel
     *
     * @return string|null
     */
    static function
    getExecutorFunctionClassName(
        $ajaxRequestModel
    ): ?string {
        return CBModel::valueAsName(
            $ajaxRequestModel,
            'CB_AjaxRequest_executorFunctionClassName_property'
        );
    }
    /* getExecutorFunctionClassName() */



    /**
     * @param object $ajaxRequestModel
     *
     * @return string|null
     */
    static function
    getExecutorFunctionName(
        $ajaxRequestModel
    ): ?string {
        return CBModel::valueAsName(
            $ajaxRequestModel,
            'CB_AjaxRequest_executorFunctionName_property'
        );
    }
    /* getExecutorFunctionName() */

}

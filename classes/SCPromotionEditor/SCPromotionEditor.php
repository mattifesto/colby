<?php

final class SCPromotionEditor {

    /* -- CBHTMLOutput interfaces -- -- -- -- -- */



    /**
     * @return [string]
     */
    static function CBHTMLOutput_JavaScriptURLs(): array {
        return [
            Colby::flexpath(__CLASS__, 'v663.js', scliburl()),
        ];
    }



    /**
     * @return [string]
     */
    static function CBHTMLOutput_requiredClassNames(): array {
        $promotionExecutorRegistrationModels = (
            CBModels::fetchModelsByClassName2(
                'SCPromotionExecutorRegistration'
            )
        );

        $promotionExecutorClassNames = array_map(
            function ($promotionExecutorRegistrationModel) {
                return $promotionExecutorRegistrationModel->executorClassName;
            },
            $promotionExecutorRegistrationModels
        );

        $requiredClassNames = array_map(
            function ($promotionExecutorClassName) {
                return "{$promotionExecutorClassName}Editor";
            },
            $promotionExecutorClassNames
        );

        return array_merge(
            $requiredClassNames,
            [
                'CBAjax',
                'CBConvert',
                'CBModel',
                'CBUI',
                'CBUIPanel',
                'CBUISpecEditor',
                'CBUIStringEditor',
                'CBUIUnixTimestampEditor',
            ]
        );
    }
    /* CBHTMLOutput_requiredClassNames() */

}

<?php

/**
 * @deprecated 2018_03_21
 *
 *      This is a temporary class to upgrade the few remaining CBFlexBoxView
 *      models to CBContainerView2 models.
 */
final class CBFlexBoxView {

    /**
     * @return void
     */
    static function CBInstall_install(): void {
        CBViewCatalog::installView(
            __CLASS__,
            (object)[
                'isUnsupported' => true,
            ]
        );
    }


    /**
     * @return [string]
     */
    static function CBInstall_requiredClassNames(): array {
        return [
            'CBViewCatalog',
        ];
    }


    /**
     * @param object $spec
     *
     * @return object
     *
     *      This function returns an empty model because all models should
     *      build, however this model gets upgraded so there should
     *      theoretically be no models of this class remaining.
     */
    static function CBModel_build(stdClass $spec): stdClass {
        return (object)[];
    }


    /**
     * @param object $spec
     *
     * @return object
     */
    static function CBModel_upgrade(stdClass $spec): stdClass {
        $specAsJSONAsMessage = CBMessageMarkup::stringToMessage(
            CBConvert::valueToPrettyJSON($spec)
        );

        $message = <<<EOT

            A CBFlexBoxView was upgraded to a CBContainerView2

            --- pre\n{$specAsJSONAsMessage}
            ---

EOT;

        CBLog::log(
            (object)[
                'message' => $message,
                'sourceClassName' => __CLASS__,
                'sourceID' => '7a17c8d3bdc1a02f3930f873ddd7df8f78ae3bfd',
            ]
        );

        $spec->className = 'CBContainerView2';

        if (CBModel::valueToString($spec, 'flexWrap') == 'wrap') {
            $spec->CSSClassNames = 'flow';
        }

        unset($spec->flexWrap);

        return $spec;
    }
    /* CBModel_upgrade() */
}

<?php

/**
 * @deprecated 2018.03.21
 *
 *      This is a temporary class to upgrade the few remaining CBFlexBoxView
 *      models to CBContainerView2 models.
 */
final class CBFlexBoxView {

    /**
     * @return void
     */
    static function CBInstall_install(): void {
        CBViewCatalog::installView(__CLASS__, (object)['isUnsupported' => true]);
    }

    /**
     * @return [string]
     */
    static function CBInstall_requiredClassNames(): array {
        return ['CBViewCatalog'];
    }

    /**
     * @param model $spec
     *
     * @return model
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

        CBLog::log((object)[
            'message' => $message,
            'sourceClassName' => __CLASS__,
            'sourceID' => '7a17c8d3bdc1a02f3930f873ddd7df8f78ae3bfd',
        ]);

        $spec->className = 'CBContainerView2';

        if (CBModel::valueToString($spec, 'flexWrap') == 'wrap') {
            $spec->CSSClassNames = 'flow';
        }

        unset($spec->flexWrap);

        return $spec;
    }
}

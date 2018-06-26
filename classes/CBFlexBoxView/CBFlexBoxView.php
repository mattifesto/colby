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
        $specAsMarkup = CBMessageMarkup::stringToMarkup(
            CBConvert::valueToPrettyJSON($spec)
        );

        CBLog::log((object)[
            'className' => __CLASS__,
            'message' => <<<EOT

                A CBFlexBoxView was upgraded to a CBContainerView2

                --- pre\n{$specAsMarkup}
                ---

EOT
        ]);

        $spec->className = 'CBContainerView2';

        if (CBModel::valueToString($spec, 'flexWrap') == 'wrap') {
            $spec->CSSClassNames = 'flow';
        }

        unset($spec->flexWrap);

        return $spec;
    }
}

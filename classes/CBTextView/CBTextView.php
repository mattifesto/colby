<?php

/**
 * @deprecated 2018_03_21
 *
 *      This is a temporary class to upgrade the few remaining CBTextView
 *      models to CBMessageView models.
 */
final class CBTextView {

    /* -- CBInstall interfaces -- -- -- -- -- */

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


    /* -- CBModel interfaces -- -- -- -- -- */

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
            'sourceID' => '00754cfbd72404dc44861189397751acabdc4ce7',
            'message' => <<<EOT

                A CBTextView was upgraded to a CBMessageView

                --- pre\n{$specAsMarkup}
                ---

EOT
        ]);

        $spec->className = 'CBMessageView';
        $spec->markup = CBMessageMarkup::stringToMarkup(
            CBModel::valueToString($spec, 'text')
        );

        unset($spec->text);

        return $spec;
    }
}

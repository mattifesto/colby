<?php

/**
 * @deprecated 2018.03.21
 *
 *      This is a temporary class to upgrade the few remaining CBTextBoxView
 *      models to CBMessageView models.
 */
final class CBTextBoxView {

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
            'sourceID' => 'f25ecc1f2d21853500bf58b652d9a256db8be0d2',
            'message' => <<<EOT

                A CBTextBoxView was upgraded to a CBMessageView

                --- pre\n{$specAsMarkup}
                ---

EOT
        ]);

        $titleAsMarkup = CBMessageMarkup::stringToMarkup(
            CBModel::valueToString($spec, 'titleAsMarkaround')
        );

        $contentAsMarkup = CBMessageMarkup::stringToMarkup(
            CBModel::valueToString($spec, 'contentAsMarkaround')
        );

        $spec->className = 'CBMessageView';
        $spec->markup = <<<EOT

--- h1
{$titleAsMarkup}
---

{$contentAsMarkup}

EOT;

        unset($spec->contentAsMarkaround);
        unset($spec->titleAsMarkaround);

        return $spec;
    }
}

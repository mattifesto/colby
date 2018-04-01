<?php

/**
 * @deprecated 2018.03.21
 *
 *      This is a temporary class to upgrade the few remaining CBFlexBoxView
 *      models to CBContainerView2 models.
 */
final class CBFlexBoxView {

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

    /**
     * @deprecated 2018.04.01
     *
     *      This function just deletes a page that used to be a test page for
     *      this class. Once it has run once on each site install for this class
     *      can be removed.
     *
     * @return void
     */
    static function CBInstall_install(): void {
        $pageID = 'b47f423d063d224ff22a415034c0f66b7b9f5abe';

        CBDB::transaction(function () use ($pageID) {
            CBModels::deleteByID($pageID);
        });
    }

    /**
     * @return [string]
     */
    static function CBInstall_requiredClassNames(): array {
        return ['CBModels', 'CBTasks2', 'CBLog', 'CBPages', 'CBViewPage'];
    }
}

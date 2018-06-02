<?php

/**
 * @deprecated 2018.03.21
 *
 *      This is a temporary class to upgrade the few remaining CBTextView
 *      models to CBMessageView models.
 */
final class CBTextView {

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

    /**
     * @deprecated 2018.03.31
     *
     *      This function just deletes a page that used to be a test page for
     *      this class. Once it has run once on each site install for this class
     *      can be removed.
     *
     * @return void
     */
    static function CBInstall_install(): void {
        $pageID = '1814177fa26d544f1cd3cdd264442d03a33a48d6';

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

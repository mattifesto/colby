<?php

/**
 * @deprecated 2018.03.21
 *
 *      This is a temporary class to upgrade the few remaining CBTextBoxView
 *      models to CBMessageView models.
 */
final class CBTextBoxView {

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
        $pageID = 'cb2679b7268a68cfcb3fa42e4feeabb456e719ed';

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

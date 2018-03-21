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

        return $spec;
    }

    /**
     * @return void
     */
    static function CBInstall_install(): void {
        $pageID = 'cb2679b7268a68cfcb3fa42e4feeabb456e719ed';
        $pageSpec = (object)[
            'className' => 'CBViewPage',
            'ID' => $pageID,
            'title' => 'CBTextBoxView Upgrade Test',
            'sections' => [
                (object)[
                    'className' => 'CBTextBoxView',
                    'titleAsMarkaround' => 'This is the Title',
                    'contentAsMarkaround' => <<<EOT

                        This is the first paragraph.

                        This is the second paragraph the talks about {The Wind
                        and the Willows}.

EOT
                ],
            ],
        ];

        CBDB::transaction(function () use ($pageID, $pageSpec) {
            CBModels::deleteByID($pageID);
            CBModels::save($pageSpec);
        });
    }

    /**
     * @return [string]
     */
    static function CBInstall_requiredClassNames(): array {
        return ['CBModels', 'CBTasks2', 'CBLog', 'CBPages', 'CBViewPage'];
    }
}

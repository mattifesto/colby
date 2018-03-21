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
            'message' => <<<EOT

                A CBTextView was upgraded to a CBMessageView

                --- pre\n{$specAsMarkup}
                ---

EOT
        ]);

        $textAsMarkup = CBMessageMarkup::stringToMarkup(
            CBModel::valueToString($spec, 'text')
        );

        $spec->className = 'CBMessageView';
        $spec->markup = <<<EOT

            {$textAsMarkup}

EOT;

        return $spec;
    }

    /**
     * @return void
     */
    static function CBInstall_install(): void {
        $pageID = '1814177fa26d544f1cd3cdd264442d03a33a48d6';
        $pageSpec = (object)[
            'className' => 'CBViewPage',
            'ID' => $pageID,
            'title' => 'CBTextView Upgrade Test',
            'sections' => [
                (object)[
                    'className' => 'CBTextView',
                    'text' => <<<EOT

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

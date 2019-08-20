<?php

/**
 * @NOTE 2018_11_19
 *
 *      This class currently behaves as an admin page for editing models.
 *      However, admin page classes should end in "Admin". This is becoming an
 *      issue because model editors on public facing pages is becoming more
 *      common and this class is using a potentially important non-admin
 *      specific class name.
 */
class CBModelEditor {

    /* -- CBAdmin interfaces -- -- -- -- -- */

    /**
     * @return [string]
     */
    static function CBAdmin_menuNamePath(): array {
        return [
            'models',
        ];
    }


    /**
     * @return void
     */
    static function CBAdmin_render(): void {
        CBHTMLOutput::pageInformation()->title = 'Edit Model';
    }


    /* -- CBHTMLOutput interfaces -- -- -- -- -- */

    /**
     * @return [string]
     */
    static function CBHTMLOutput_requiredClassNames(): array {
        $originalSpec = CBModelEditor::fetchOriginalSpec();

        return [
            'CBUI',
            'CBUINavigationView',
            'CBUISpecEditor',
            'CBUISpecSaver',
            'Colby',
            $originalSpec->className . 'Editor',
        ];
    }


    /**
     * @return [string]
     */
    static function CBHTMLOutput_JavaScriptURLs(): array {
        return [
            Colby::flexpath(__CLASS__, 'v514.js', cbsysurl()),
        ];
    }


    /**
     * @return [[<name>, <value>]]
     */
    static function CBHTMLOutput_JavaScriptVariables(): array {
        return [
            [
                'CBModelEditor_originalSpec',
                CBModelEditor::fetchOriginalSpec(),
            ],
        ];
    }


    /* -- functions -- -- -- -- -- */

    private static function fetchOriginalSpec(): stdClass {
        static $originalSpec;
        static $originalSpecHasBeenFetched = false;

        if ($originalSpecHasBeenFetched) {
            return $originalSpec;
        }

        $ID = cb_query_string_value('ID');
        $copyID = cb_query_string_value('copyID');
        $templateClassName = cb_query_string_value('templateClassName');

        if (!CBHex160::is($ID)) {
            throw new CBException(
                "No model ID was specified.",
                '',
                '2db0099f3f9cf6dd7db8220a00a1e944de834758'
            );
        } else {
            $originalSpec = CBModels::fetchSpecByID($ID);

            if (empty($originalSpec) && CBHex160::is($copyID)) {
                $copiedSpec = CBModels::fetchSpecByIDNullable($copyID);

                if ($copiedSpec === null) {
                    throw new CBException(
                        "No model exists to copy with the ID {$copyID}",
                        '',
                        'fcfe83a69d00d91d61360ad1c376b71e0a063393'
                    );
                }
                $originalSpec = CBModel::copy($copiedSpec, $ID);
            }

            if (empty($originalSpec)) {
                if (empty($templateClassName)) {
                    throw new CBException(
                        "No model exists with the ID {$ID}.",
                        '',
                        'ad535e8eb3c2f8f1d84cb29cd5781e926415a0f1'
                    );
                } else if (
                    is_callable(
                        $function = "{$templateClassName}::CBModelTemplate_spec"
                    )
                ) {
                    $originalSpec = call_user_func($function);
                    $originalSpec->ID = $ID;
                } else {
                    throw new CBException(
                        (
                            "No model template class exists with the " .
                            "class name \"{$templateClassName}\""
                        ),
                        '',
                        '8dab808b65ec7529abeba0d6c7b7788a6fdd325f'
                    );
                }
            }
        }

        /**
         * The original spec is upgraded before being sent to the editor to
         * handle cases where a system update has recently happened that
         * includes new upgrades for this class of model. All models are
         * scheduled to be upgraded after an update, but it does take some time.
         * A user may be editing a model before its upgrade task has run.
         */
        $originalSpec = CBModel::upgrade($originalSpec);

        $originalSpecHasBeenFetched = true;

        return $originalSpec;
    }
    /* fetchOriginalSpec() */
}

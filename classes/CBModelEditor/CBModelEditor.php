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
     * @return string
     */
    static function CBAdmin_getUserGroupClassName(): string {
        return 'CBAdministratorsUserGroup';
    }



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
    static function
    CBHTMLOutput_JavaScriptURLs(
    ): array {
        return [
            Colby::flexpath(
                __CLASS__,
                'v675.3.js',
                cbsysurl()
            ),
        ];
    }
    /* CBHTMLOutput_JavaScriptURLs() */



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
    /* CBHTMLOutput_JavaScriptVariables() */



    /**
     * @return [string]
     */
    static function CBHTMLOutput_requiredClassNames(): array {
        $originalSpec = CBModelEditor::fetchOriginalSpec();

        $specEditorClassName = (
            $originalSpec->className .
            'Editor'
        );

        return [
            'CBSpecSaver',
            'CBUI',
            'CBUINavigationView',
            'CBUIPanel',
            'CBUISpecEditor',
            'Colby',
            $specEditorClassName,
        ];
    }
    /* CBHTMLOutput_requiredClassNames() */



    /* -- functions -- -- -- -- -- */



    /**
     * @return object
     */
    private static function fetchOriginalSpec(): stdClass {
        static $originalSpec;
        static $originalSpecHasBeenFetched = false;

        if ($originalSpecHasBeenFetched) {
            return $originalSpec;
        }

        $ID = cb_query_string_value(
            'ID'
        );

        $modelToCopyCBID = cb_query_string_value(
            'copyID'
        );

        $templateClassName = cb_query_string_value(
            'templateClassName'
        );

        $suggestedSpecAsJSON = cb_query_string_value(
            'suggestedSpecAsJSON'
        );

        if (!CBID::valueIsCBID($ID)) {
            throw new CBException(
                "No model ID was specified.",
                '',
                '2db0099f3f9cf6dd7db8220a00a1e944de834758'
            );
        }

        $originalSpec = CBModels::fetchSpecByID($ID);


        /* copy another model */

        if (
            empty($originalSpec) &&
            CBID::valueIsCBID($modelToCopyCBID)
        ) {
            $modelToCopySpec = CBModels::fetchSpecByIDNullable(
                $modelToCopyCBID
            );

            if ($modelToCopySpec === null) {
                throw new CBException(
                    "No model exists to copy with the ID {$modelToCopyCBID}",
                    '',
                    'fcfe83a69d00d91d61360ad1c376b71e0a063393'
                );
            }

            $originalSpec = CBModel::copy(
                $modelToCopySpec,
                $ID
            );
        }


        /* use a template to generate the spec */

        if (
            empty($originalSpec) &&
            !empty($templateClassName)
        ) {
            $functionName = "{$templateClassName}::CBModelTemplate_spec";

            if (is_callable($functionName)) {
                $originalSpec = call_user_func(
                    $functionName
                );

                $originalSpec->ID = $ID;
            } else {
                throw new CBException(
                    CBConvert::stringToCleanLine(<<<EOT

                        No model template class exists with the class name
                        "{$templateClassName}"

                    EOT),
                    '',
                    '8dab808b65ec7529abeba0d6c7b7788a6fdd325f'
                );
            }
        }

        /* use a suggested spec passed in the URL */

        if (
            empty($originalSpec) &&
            !empty($suggestedSpecAsJSON)
        ) {
            $originalSpec = json_decode(
                $suggestedSpecAsJSON
            );

            $originalSpec->ID = $ID;

            unset($originalSpec->version);
        }


        /* there is no source of a spec to edit */

        if (empty($originalSpec)) {
            throw new CBException(
                "No model exists with the ID {$ID}.",
                '',
                'ad535e8eb3c2f8f1d84cb29cd5781e926415a0f1'
            );
        }


        /**
         * The original spec is upgraded before being sent to the editor to
         * handle cases where a system update has recently happened that
         * includes new upgrades for this class of model. All models are
         * scheduled to be upgraded after an update, but it does take some time.
         * A user may be editing a model before its upgrade task has run.
         */

        $originalSpec = CBModel::upgrade(
            $originalSpec
        );

        $originalSpecHasBeenFetched = true;

        return $originalSpec;
    }
    /* fetchOriginalSpec() */

}

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

    private static $originalSpec = null;
    private static $message = '';

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
        $ID = cb_query_string_value('ID');
        $copyID = cb_query_string_value('copyID');
        $templateClassName = cb_query_string_value('templateClassName');
        $message = '';

        if (!CBHex160::is($ID)) {
            $message = "No model ID was specified.";
            $originalSpec = null;
        } else {
            $originalSpec = CBModels::fetchSpecByID($ID);

            if (empty($originalSpec) && CBHex160::is($copyID)) {
                $originalSpec = CBModels::fetchSpecByID($copyID);

                if (!empty($originalSpec)) {
                    $originalSpec = CBModel::copy($originalSpec, $ID);
                }
            }

            if (empty($originalSpec)) {
                if (empty($templateClassName)) {
                    $message = "No model exists with the ID {$ID}.";
                } else if (
                    is_callable(
                        $function = "{$templateClassName}::CBModelTemplate_spec"
                    )
                ) {
                    $originalSpec = call_user_func($function);
                    $originalSpec->ID = $ID;
                } else {
                    $templateClassNameAsMarkup =
                    CBMessageMarkup::stringToMarkup($templateClassName);

                    $message = (
                        "A model template with the class name " .
                        "\"{$templateClassNameAsMarkup}\" does not exist."
                    );
                }
            }
        }

        CBModelEditor::$originalSpec = $originalSpec;
        CBModelEditor::$message = $message;

        CBHTMLOutput::pageInformation()->title = 'Edit Model';
    }


    /**
     * @return [string]
     */
    static function CBHTMLOutput_requiredClassNames(): array {
        $classNames = [
            'CBUI',
            'CBUIMessagePart',
            'CBUINavigationView',
            'CBUISectionItem4',
            'CBUISpecEditor',
            'CBUISpecSaver',
        ];

        if (CBModelEditor::$originalSpec) {
            array_push(
                $classNames,
                CBModelEditor::$originalSpec->className . 'Editor'
            );
        };

        return $classNames;
    }


    /**
     * @return [string]
     */
    static function CBHTMLOutput_JavaScriptURLs(): array {
        return [
            Colby::flexpath(__CLASS__, 'v433.js', cbsysurl()),
        ];
    }


    /**
     * @return [[<name>, <value>]]
     */
    static function CBHTMLOutput_JavaScriptVariables(): array {
        return [
            [
                'CBModelEditor_originalSpec',
                CBModelEditor::$originalSpec,
            ],
            [
                'CBModelEditor_message',
                CBModelEditor::$message,
            ],
        ];
    }
}

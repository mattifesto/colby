<?php

class CBModelEditor {

    /**
     * @return [string]
     */
    static function CBAdmin_menuNamePath(): array {
        return ['models'];
    }

    /**
     * @return void
     */
    static function CBAdmin_render(): void {
        CBHTMLOutput::pageInformation()->title = 'Edit Model';
    }

    /**
     * @return [string]
     */
    static function CBHTMLOutput_requiredClassNames(): array {
        return ['CBUI', 'CBUINavigationView', 'CBUISpecSaver'];
    }

    /**
     * @return [string]
     */
    static function CBHTMLOutput_JavaScriptURLs(): array {
        return [Colby::flexpath(__CLASS__, 'v410.js', cbsysurl())];
    }

    /**
     * @return [[<name>, <value>]]
     */
    static function CBHTMLOutput_JavaScriptVariables(): array {
        $ID = cb_query_string_value('ID');
        $templateClassName = cb_query_string_value('templateClassName');

        if (empty($ID)) {
            $message = "No model ID was specified.";
            $originalSpec = null;
        } else {
            $originalSpec = CBModels::fetchSpecByID($ID);

            if (empty($originalSpec)) {
                if (empty($templateClassName)) {
                    $message = "No model exists with the ID {$ID}.";
                } else  if (is_callable($function = "{$templateClassName}::CBModelTemplate_spec")) {
                    $originalSpec = call_user_func($function);
                    $originalSpec->ID = $ID;
                } else {
                    $templateClassNameAsMarkup = CBMessageMarkup::stringToMarkup($templateClassName);
                    $message = "A model template with the class name \"{$templateClassNameAsMarkup}\" does not exist.";
                }
            }
        }


        if (!empty($originalSpec)) {
            CBHTMLOutput::requireClassName("{$originalSpec->className}Editor");
        }

        return [
            ['CBModelEditor_originalSpec', $originalSpec],
            ['CBModelEditor_message', $message],
        ];
    }
}

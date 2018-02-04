<?php

class CBModelEditor {

    /**
     * @return [string]
     */
    static function CBAdmin_menuNamePath() {
        return ['models'];
    }

    /**
     * @return void
     */
    static function CBAdmin_render() {
        CBHTMLOutput::pageInformation()->title = 'Edit Model';

        $args = CBModelEditor::fetchArguments();

        if (is_callable($function = "{$args->className}::editorGroup")) {
            $group = call_user_func($function);
            if (!ColbyUser::current()->isOneOfThe($group)) {
                CBHTMLOutput::exportVariable('CBModelEditorAuthorizationFailed', true);

                ?>

                <p style="padding: 50px 20px; text-align: center">You do not
                    have the authorization required to edit this model.

                <?php

                return;
            }
        }

        CBHTMLOutput::exportVariable('CBModelEditor_modelID', $args->ID);
        CBHTMLOutput::exportVariable('CBModelEditor_modelClassName', $args->className);

        if (class_exists($editorClassName = "{$args->className}Editor")) {
            CBHTMLOutput::requireClassName($editorClassName);
        }
    }

    /**
     * @return {stdClass} | exit
     */
    private static function fetchArguments() {
        $args = new stdClass();
        $className = isset($_GET['className']) ? $_GET['className'] : null;
        $ID = isset($_GET['ID']) ? $_GET['ID'] : null;

        if ($ID === null && $className === null) {
            throw new InvalidArgumentException('Either `ID` or `className` must be specified.');
        } else if ($ID === null) {
            $ID = CBHex160::random();
            header("Location: /admin/page/?class=CBModelEditor&ID={$ID}&className={$className}");
            exit();
        } else {
            $args->ID = $ID;
            $spec = CBModels::fetchSpecByID($ID);

            if ($spec) {
                $args->className = $spec->className;
            } else if ($className) {
                $args->className = $className;
            } else {
                /**
                 * There is no model or class name so just redirect to the
                 * inspector.
                 */
                header("Location: /admin/?c=CBModelInspector&ID={$ID}");
                exit();
            }

            return $args;
        }
    }

    /**
     * @return [string]
     */
    static function CBHTMLOutput_requiredClassNames() {
        return ['CBUI', 'CBUINavigationView', 'CBUISpecSaver'];
    }

    /**
     * @return [string]
     */
    static function CBHTMLOutput_JavaScriptURLs() {
        return [Colby::flexpath(__CLASS__, 'v367.js', cbsysurl())];
    }
}

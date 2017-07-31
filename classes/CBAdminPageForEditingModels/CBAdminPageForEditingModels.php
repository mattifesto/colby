<?php

class CBAdminPageForEditingModels {

    /**
     * @return [string]
     */
    static function adminPageMenuNamePath() {
        return ['models'];
    }

    /**
     * @return stdClass
     */
    static function adminPagePermissions() {
        return (object)['group' => 'Administrators'];
    }

    /**
     * @return void
     */
    static function adminPageRenderContent() {
        CBHTMLOutput::setTitleHTML('Edit Model');
        CBHTMLOutput::setDescriptionHTML('Tools a model.');

        $args = CBAdminPageForEditingModels::fetchArguments();

        if (is_callable($function = "{$args->className}::editorGroup")) {
            $group = call_user_func($function);
            if (!ColbyUser::current()->isOneOfThe($group)) {
                CBHTMLOutput::exportVariable('CBAdminPageForEditingModelsAuthorizationFailed', true);

                ?>

                <p style="padding: 50px 20px; text-align: center">You do not
                    have the authorization required to edit this model.

                <?php

                return;
            }
        }

        CBHTMLOutput::exportVariable('CBAdminPageForEditingModels_modelID', $args->ID);
        CBHTMLOutput::exportVariable('CBAdminPageForEditingModels_modelClassName', $args->className);

        if (is_callable($function = "{$args->className}::info")) {
            CBHTMLOutput::exportVariable('CBAdminPageForEditingModels_modelClassInfo', call_user_func($function));
        }

        if (class_exists($editorClassName = "{$args->className}Editor")) {
            CBHTMLOutput::requireClassName($editorClassName);
        }
    }

    /**
     * @return {stdClass} | exit
     */
    private static function fetchArguments() {
        $args       = new stdClass();
        $className  = isset($_GET['className']) ? $_GET['className'] : null;
        $ID         = isset($_GET['ID']) ? $_GET['ID'] : null;

        if ($ID === null && $className === null) {
            throw new InvalidArgumentException('Either `ID` or `className` must be specified.');
        } else if ($ID === null) {
            $ID = CBHex160::random();
            header("Location: /admin/page/?class=CBAdminPageForEditingModels&ID={$ID}&className={$className}");
            exit();
        } else {
            $args->ID   = $ID;
            $spec       = CBModels::fetchSpecByID($ID);

            if ($spec) {
                $args->className = $spec->className;
            } else if ($className) {
                $args->className = $className;
            } else {
                throw new InvalidArgumentException('A `className` must be specified for a new model.');
            }

            return $args;
        }
    }

    /**
     * @return [string]
     */
    static function requiredClassNames() {
        return ['CBUI', 'CBUINavigationView', 'CBUISpecSaver'];
    }

    /**
     * @return [string]
     */
    static function requiredJavaScriptURLs() {
        return [Colby::flexnameForJavaScriptForClass(CBSystemURL, __CLASS__)];
    }
}

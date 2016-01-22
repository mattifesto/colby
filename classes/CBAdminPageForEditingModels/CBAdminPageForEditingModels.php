<?php

class CBAdminPageForEditingModels {

    /**
     * @deprecated create a "{$className}Editor" class instead
     *
     * @return null
     */
    private static function loadEditingResourcesForClassName($className) {
        if (is_callable($function = "{$className}::classNamesForEditorDependencies")) {
            $classNames = call_user_func($function);
            array_walk($classNames, 'CBAdminPageForEditingModels::loadEditingResourcesForClassName');
        }

        if (is_callable($function = "{$className}::editorURLsForCSS")) {
            $URLs = call_user_func($function);
            array_walk($URLs, function($URL) { CBHTMLOutput::addCSSURL($URL); });
        }

        if (is_callable($function = "{$className}::editorURLsForJavaScript")) {
            $URLs = call_user_func($function);
            array_walk($URLs, function($URL) { CBHTMLOutput::addJavaScriptURL($URL); });
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
            header("Location: /admin/models/edit/?ID={$ID}&className={$className}");
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
     * @return null
     */
    public static function renderModelAsHTML(stdClass $model = null) {
        if (!ColbyUser::current()->isOneOfThe('Administrators')) {
            return include CBSystemDirectory . '/handlers/handle-authorization-failed.php';
        }

        $args = CBAdminPageForEditingModels::fetchArguments();

        if (is_callable($function = "{$args->className}::editorGroup")) {
            $group = call_user_func($function);

            if (!ColbyUser::current()->isOneOfThe($group)) {
                return include CBSystemDirectory . '/handlers/handle-authorization-failed.php';
            }
        }

        CBHTMLOutput::$classNameForSettings = 'CBPageSettingsForAdminPages';
        CBHTMLOutput::setTitleHTML('Edit Model');
        CBHTMLOutput::setDescriptionHTML('Edit a model');
        CBHTMLOutput::begin();
        CBHTMLOutput::requireClassName('CBUI');
        CBHTMLOutput::requireClassName('CBDefaultEditor');

        CBHTMLOutput::exportVariable('CBModelID',           $args->ID);
        CBHTMLOutput::exportVariable('CBModelClassName',    $args->className);

        if (is_callable($function = "{$args->className}::info")) {
            CBHTMLOutput::exportVariable('CBModelClassInfo', call_user_func($function));
        }

        if (class_exists($editorClassName = "{$args->className}Editor")) {
            CBHTMLOutput::requireClassName($editorClassName);
        } else {
            CBAdminPageForEditingModels::loadEditingResourcesForClassName($args->className);
        }

        CBHTMLOutput::addCSSURL(        CBAdminPageForEditingModels::URL('CBAdminPageForEditingModels.css'));
        CBHTMLOutput::addJavaScriptURL( CBAdminPageForEditingModels::URL('CBAdminPageForEditingModels.js'));

        $spec                           = new stdClass();
        $spec->selectedMenuItemName     = 'models';
        CBAdminPageMenuView::renderModelAsHTML(CBAdminPageMenuView::specToModel($spec));

        echo '<main class="CBAdminPageForEditingModels CBUIRoot"></main>';

        CBAdminPageFooterView::renderModelAsHTML();
        CBHTMLOutput::render();
    }

    /**
     * @return {string}
     */
    public static function URL($filename) {
        return CBSystemURL . "/classes/CBAdminPageForEditingModels/{$filename}";
    }
}

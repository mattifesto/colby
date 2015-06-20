<?php

class CBAdminPageForEditingModels {

    /**
     * @return null
     */
    private static function loadEditingResourcesForClassName($className) {
        if (is_callable($function = "{$className}::editorURLsForCSS")) {
            $URLs = call_user_func($function);
            array_walk($URLs, 'CBHTMLOutput::addCSSURL');
        }

        if (is_callable($function = "{$className}::editorURLsForJavaScript")) {
            $URLs = call_user_func($function);
            array_walk($URLs, 'CBHTMLOutput::addJavaScriptURL');
        }
    }

    /**
     * @return {stdClass} | exit
     */
    private static function fetchClassName() {
        $className  = isset($_GET['className']) ? $_GET['className'] : null;
        $ID         = isset($_GET['ID']) ? $_GET['ID'] : null;

        if ($ID === null && $className === null) {
            throw new InvalidArgumentException('Either `ID` or `className` must be specified.');
        } else if ($ID === null) {
            $ID = CBHex160::random();
            header("Location: /admin/models/edit/?ID={$ID}&className={$className}");
            exit();
        } else {
            $spec = CBModels::fetchSpecByID($ID);

            if ($spec) {
                return $spec->className;
            } else if ($className) {
                return $className;
            } else {
                throw new InvalidArgumentException('A `className` must be specified for a new model.');
            }
        }
    }

    /**
     * @return null
     */
    public static function renderModelAsHTML(stdClass $model = null) {
        if (!ColbyUser::current()->isOneOfThe('Administrators')) {
            return include CBSystemDirectory . '/handlers/handle-authorization-failed.php';
        }

        $className = CBAdminPageForEditingModels::fetchClassName();

        if (is_callable($function = "{$className}::editorGroup")) {
            $group = call_user_func($function);

            if (!ColbyUser::current()->isOneOfThe($group)) {
                return include CBSystemDirectory . '/handlers/handle-authorization-failed.php';
            }
        }

        CBHTMLOutput::setTitleHTML('Edit Model');
        CBHTMLOutput::setDescriptionHTML('Edit a model');
        CBHTMLOutput::begin();

        include CBSystemDirectory . '/sections/admin-page-settings.php';

        $spec                           = new stdClass();
        $spec->selectedMenuItemName     = 'edit';
        CBAdminPageMenuView::renderModelAsHTML(CBAdminPageMenuView::specToModel(new stdClass()));

        CBHTMLOutput::addJavaScriptURL(self::URL('CBAdminPageForEditingModels.js'));

        CBAdminPageForEditingModels::loadEditingResourcesForClassName($className);

        echo '<main>Hello, world!</main>';

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

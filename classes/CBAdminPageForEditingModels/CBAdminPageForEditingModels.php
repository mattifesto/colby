<?php

class CBAdminPageForEditingModels {

    /**
     * @return null
     */
    private static function loadEditingResourcesForSpec($spec) {
        $className = $spec->className;

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
    private static function makeSpec() {
        $className  = isset($_GET['className']) ? $_GET['className'] : null;
        $ID         = isset($_GET['ID']) ? $_GET['ID'] : null;

        if ($ID === null && $className === null) {
            throw new InvalidArgumentException('Either `ID` or `className` must be specified.');
        } else if ($ID === null) {
            $ID = Colby::random160();
            header("Location: /admin/edit?ID={$ID}&className={$className}");
            exit();
        } else {
            $spec = CBModels::fetchSpec($ID);

            if ($spec === false && $className === null) {
                throw new InvalidArgumentException('A `className` must be specified for a new model.');
            } else if ($spec === false) {
                $spec = CBView::modelWithClassName($className);
            }
        }

        return $spec;
    }

    /**
     * @return null
     */
    public static function renderModelAsHTML(stdClass $model = null) {
        if (!ColbyUser::current()->isOneOfThe('Administrators')) {
            return include CBSystemDirectory . '/handlers/handle-authorization-failed.php';
        }

        $spec = CBAdminPageForEditingModels::makeSpec();

        // TODO: if (!CBAdminPageForEditingModels::useCanEditSpec($spec)) { ... }

        CBHTMLOutput::setTitleHTML('Edit Model');
        CBHTMLOutput::setDescriptionHTML('Edit a model');
        CBHTMLOutput::begin();

        include CBSystemDirectory . '/sections/admin-page-settings.php';

        CBAdminPageMenuView::renderModelAsHTML(CBAdminPageMenuView::specToModel(new stdClass()));

        CBHTMLOutput::addJavaScriptURL(self::URL('CBAdminPageForEditingModels.js'));

        CBAdminPageForEditingModels::loadEditingResourcesForSpec($spec);

        echo '<main>Hello, world!</main>';

        CBAdminPageFooterView::renderModelAsHTML();
        CBHTMLOutput::render();
    }

    /**
     * @return {string}
     */
    public static function URL($filename) {
        return CBSystemURL . "classes/CBAdminPageForEditingModels/{$filename}";
    }
}

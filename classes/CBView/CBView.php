<?php

final class CBView {

    /**
     * @param string $keyword
     *
     *      The keyword used in the template to refer to the local element.
     *      Usually it's "view". The keyword can be escaped in the template by
     *      prefixing it with a backslash: "\view". The keyword can be prefixed
     *      by a dot to enabled sometimes required scenarios like:
     *
     *      a.view { ... }
     *
     * @param string $localCSSTemplate
     * @param string $selector
     *
     *      Examples: ".MyClass", "#MyID", ".ID_80d0c20a7d38fb6995f663e8e253b3d77f17fe2c"
     *
     * @return string
     */
    static function localCSSTemplateToLocalCSS($localCSSTemplate, $keyword, $selector) {
        $escapedKeywordPlaceholder = CBHex160::random();
        $localCSS = $localCSSTemplate;

        // Replace escaped keywords with a temporary placeholder.
        $localCSS = preg_replace("/\\\\{$keyword}/", $escapedKeywordPlaceholder, $localCSS);

        // Replace the keyword, optionally prefixed with a dot, with the selector.
        $localCSS = preg_replace("/\\.?{$keyword}/", $selector, $localCSS);

        // Replace the temporary placeholder with the unescaped keyword.
        $localCSS = preg_replace("/{$escapedKeywordPlaceholder}/", $keyword, $localCSS);

        if (preg_match('/<\\/style *>/', $localCSS, $matches)) {
            throw new RuntimeException("The \$localCSSTemplate argument contains the string \"{$matches[0]}\" which is not allowed for security reasons.");
        }

        return $localCSS;
    }

    /**
     * @deprecated use CBModel::toSearchText()
     */
    static function modelToSearchText(stdClass $model) {
        $className = CBModel::value($model, 'className', '');

        if ($className != 'CBView') {
            return CBModel::toSearchText($model);
        } else {
            return null;
        }
    }

    /**
     * @deprecated use (object)['className' => '<desired class name>']
     *
     * @return stdClass
     */
    static function modelWithClassName($className) {
        $model              = new stdClass();
        $model->className   = (string)$className;

        return $model;
    }

    /**
     * Always use this function to render a view instead of calling the render
     * interfaces directly on the view class. This function will also make sure
     * that all of the view class's dependencies are included.
     *
     * @param object? $model
     *
     * @return null
     */
    static function render(stdClass $model) {
        $className = CBModel::value($model, 'className', '');

        if (empty($className)) {
            return;
        }

        CBHTMLOutput::requireClassName($className);

        if (is_callable($function = "{$className}::CBView_render")) {
            return call_user_func($function, $model);
        } else if (is_callable($function = "{$className}::renderModelAsHTML")) { // deprecated
            return call_user_func($function, $model);
        } else if (CBSitePreferences::debug()) {
            $classNameAsComment = ': ' . str_replace('--', ' - - ', $className);

            echo "<!-- CBView::renderModelAsHTML() found no CBView_render() function for the class: \"{$classNameAsComment}\" -->";
        }
    }

    /**
     * @deprecated use CBView::render()
     */
    static function renderModelAsHTML(stdClass $model) {
        $className = CBModel::value($model, 'className', '');

        if ($className != 'CBView') {
            return CBView::render($model);
        }
    }

    /**
     * This function makes it easy to create a view spec and render it from PHP
     * code. The code could easily call these lines itself or most of the time
     * even call the view's functions directly, but allowing rendering a spec
     * with a single function call saves many lines of code over an entire site.
     *
     * @param object $spec
     *
     * @return null
     */
    static function renderSpec(stdClass $spec) {
        if ($model = CBModel::specToModel($spec)) {
            CBView::render($model);
        } else {
            $className = CBModel::value($spec, 'className', '');
            $className = str_replace('--', ' - - ', $className);

            echo "<!-- CBView::renderSpecAsHTML() could not convert a '{$className}' spec into a model. -->";
        }
    }

    /**
     * @deprecated use CBView::renderSpec()
     */
    static function renderSpecAsHTML(stdClass $spec) {
        CBView::renderSpec($spec);
    }
}

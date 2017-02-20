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
     * @return string
     */
    static function modelToSearchText(stdClass $model = null) {
        if (isset($model->className) && $model->className != 'CBView') {
            $function = "{$model->className}::modelToSearchText";

            if (is_callable($function)) {
                return call_user_func($function, $model);
            }
        }

        return '';
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
     * Always use this function to render a view instead of calling
     * renderModelAsHTML() directly on the class. This function will also make
     * sure that all of the view class's dependencies are included.
     *
     * @param stdClass? $model
     *
     * @return null
     */
    static function renderModelAsHTML(stdClass $model = null) {
        if (isset($model->className) && $model->className != 'CBView') {
            CBHTMLOutput::requireClassName($model->className);

            $function = "{$model->className}::renderModelAsHTML";

            if (is_callable($function)) {
                return call_user_func($function, $model);
            }
        }

        if (CBSitePreferences::debug()) {
            $modelAsJSONAsHTML = ': ' . str_replace('--', ' - - ', json_encode($model));
        } else {
            $modelAsJSONAsHTML = '';
        }

        echo "<!-- CBView::renderModelAsHTML() default output{$modelAsJSONAsHTML} -->";
    }

    /**
     * This function makes it easy to create a view spec and render it from PHP
     * code. The code could easily call these lines itself or most of the time
     * even call the view's functions directly, but allowing rendering a spec
     * with a single function call saves many lines of code over an entire site.
     *
     * @param stdClass $spec
     *
     * @return null
     */
    static function renderSpecAsHTML(stdClass $spec) {
        $model = CBView::specToModel($spec);
        CBView::renderModelAsHTML($model);
    }

    /**
     * @deprecated use CBModel::specToOptionalModel
     *
     * This function transforms a view specification into a model. This
     * function always succeeds. If the view class has no `specToModel`
     * function the model will be a copy of the specification. If the
     * specification has no `className` property, the `className` property
     * on the model will be set to "CBView".
     *
     * @return stdClass
     */
    static function specToModel(stdClass $spec = null) {
        if (isset($spec->className) && $spec->className != 'CBView') {
            $function = "{$spec->className}::specToModel";

            if (is_callable($function)) {
                return call_user_func($function, $spec);
            }
        }

        if ($spec) {
            $model = json_decode(json_encode($spec));
        } else {
            $model = new stdClass();
        }

        $model->className = isset($model->className) ? $model->className : 'CBView';

        return $model;
    }
}

<?php

final class CBView {

    /* -- CBHTMLOutput interfaces -- -- -- -- -- */



    /**
     * @return [string]
     */
    static function CBHTMLOutput_JavaScriptURLs(): array {
        return [
            Colby::flexpath(__CLASS__, 'v470.js', cbsysurl()),
        ];
    }



    /**
     * @return [string]
     */
    static function CBHTMLOutput_requiredClassNames(): array {
        return [
            'CBModel',
        ];
    }



    /* -- functions -- -- -- -- -- */



    /**
     * CSS Template Documentation
     *
     *      1. Get the trimmed CSS template string from the spec
     *      2. If it's not empty, create a unique CSS class name
     *      3. Add the unique CSS class name to the list of class names stored
     *         with the model
     *      4. Generate the local CSS using this function and store it with the
     *         model
     *
     * Sample code:
     *
     *      $CSSTemplate = trim(
     *          CBConvert::valueToString(CBModel::value($spec, 'CSSTemplate'))
     *      );
     *
     *      if ($CSSTemplate !== '') {
     *          $uniqueCSSClassName = 'ID_' . CBID::generateRandomCBID();
     *          $model->CSSClassNames[] = $uniqueCSSClassName;
     *          $model->CSS = CBView::CSSTemplateToCSS(
     *              $CSSTemplate,
     *              $uniqueCSSClassName
     *          );
     *      }
     *
     * @param string $CSSTemplate
     * @param string $uniqueCSSClassName
     *
     * @return string
     */
    static function CSSTemplateToCSS(
        string $CSSTemplate,
        string $uniqueCSSClassName
    ): string {
        return CBView::localCSSTemplateToLocalCSS(
            $CSSTemplate,
            'view',
            ".{$uniqueCSSClassName}"
        );
    }



    /**
     * Filters the subviews of a view using a callback function. This function
     * will recurse into deeper subviews.
     *
     * @param model $view
     * @param callable $callback
     *
     *      The callback function should accept one mixed type parameter which
     *      will be the model of each subview.
     *
     *      If the callback returns true the subview will be kept, otherwise the
     *      subview will be removed.
     *
     * @return void
     */
    static function filterSubviews(stdClass $view, callable $callback): void {
        $subviews = CBView::getSubviews($view);

        if (empty($subviews)) {
            return;
        } else {
            $subviews = array_values(array_filter($subviews, $callback));

            foreach ($subviews as $subview) {
                CBView::filterSubviews($subview, $callback);
            }

            CBView::setSubviews($view, $subviews);
        }
    }



    /**
     * Finds a subview in an object that may have subviews.
     *
     * NOTE
     *
     *      This function is intended to be used where there is likely to be at
     *      most one subview matching the criteria. It is not intended to find
     *      all subviews with the given criteria.
     *
     * @param object $model
     *
     *      This can be a model or a spec. This object is not checked for the
     *      search criteria because it may be a page model which therefore is
     *      not a subview.
     *
     * @param string $key
     * @param mixed $value
     *
     *      The $value parameter is compared against the model property value
     *      with the equal comparison operator (==).
     *
     * @return ?object
     */
    static function findSubview($model, $key, $value): ?stdClass {
        $subviews = CBView::getSubviews($model);

        foreach ($subviews as $view) {
            if (isset($view->{$key}) && $view->{$key} == $value) {
                return $view;
            }

            $result = CBView::findSubview($view, $key, $value);

            if ($result !== null) {
                return $result;
            }
        }

        return null;
    }



    /**
     * @deprecated use CBView::CSSTemplateToCSS()
     *
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
    static function localCSSTemplateToLocalCSS(
        $localCSSTemplate,
        $keyword,
        $selector
    ) {
        $escapedKeywordPlaceholder = CBID::generateRandomCBID();
        $localCSS = $localCSSTemplate;

        // Replace escaped keywords with a temporary placeholder.
        $localCSS = preg_replace(
            "/\\\\{$keyword}/",
            $escapedKeywordPlaceholder,
            $localCSS
        );

        // Replace the keyword, optionally prefixed with a dot, with the selector.
        $localCSS = preg_replace(
            "/\\.?{$keyword}/",
            $selector,
            $localCSS
        );

        // Replace the temporary placeholder with the unescaped keyword.
        $localCSS = preg_replace(
            "/{$escapedKeywordPlaceholder}/",
            $keyword,
            $localCSS
        );

        if (preg_match('/<\\/style *>/', $localCSS, $matches)) {
            throw new RuntimeException(
                "The \$localCSSTemplate argument contains the string " .
                "\"{$matches[0]}\" which is not allowed for security " .
                "reasons."
            );
        }

        return $localCSS;
    }



    /**
     * @param model $model
     * @param callable $callback
     *
     *      The callback function receives a single parameter, a clone of the
     *      model's current array of subviews. The callback should return its
     *      desired array of subviews.
     *
     * @return void
     */
    static function modifySubviews(stdClass $model, callable $callback): void {
        $clonedSubviews = CBModel::clone(
            CBView::getSubviews($model)
        );

        CBView::setSubviews(
            $model,
            call_user_func($callback, $clonedSubviews)
        );
    }



    /**
     * Always use this function to render a view instead of calling the render
     * interfaces directly on the view class. This function will also make sure
     * that all of the view class's dependencies are included.
     *
     * @param object? $model
     *
     * @return void
     */
    static function render(stdClass $model): void {
        $className = CBModel::valueToString(
            $model,
            'className'
        );

        if (empty($className)) {
            return;
        }

        CBHTMLOutput::requireClassName($className);

        if (is_callable($function = "{$className}::CBView_render")) {
            call_user_func($function, $model);
        }

        else if (CBSitePreferences::debug()) {
            $classNameAsComment = ': ' . str_replace('--', ' - - ', $className);

            echo (
                "<!-- CBView::render() found no CBView_render() function " .
                "for the class: \"{$classNameAsComment}\" -->"
            );
        }
    }
    /* render() */


    /**
     * This function makes it easy to create a view spec and render it from PHP
     * code. The code could easily call these lines itself or most of the time
     * even call the view's functions directly, but allowing rendering a spec
     * with a single function call saves many lines of code over an entire site.
     *
     * @param object $spec
     *
     * @return void
     */
    static function renderSpec(stdClass $viewSpec): void {
        $viewModel = CBModel::build($viewSpec);

        CBView::render($viewModel);
    }



    /**
     * @deprecated use CBView::renderSpec()
     */
    static function renderSpecAsHTML(stdClass $spec) {
        CBView::renderSpec($spec);
    }



    /**
     * @param model $model
     *
     *      Can be a spec or a model.
     *
     * @return [model]
     */
    static function getSubviews(stdClass $model): array {
        $className = CBModel::valueToString($model, 'className');

        if (is_callable($function = "{$className}::CBView_toSubviews")) {
            return call_user_func($function, $model);
        } else {
            return CBModel::valueToArray($model, 'subviews');
        }
    }



    /**
     * @param model $model
     *
     *      Can be a spec or a model.
     *
     * @param [model] $subviews
     *
     * @return void
     */
    static function setSubviews(stdClass $model, array $subviews): void {
        $className = CBModel::valueToString($model, 'className');

        if (is_callable($function = "{$className}::CBView_setSubviews")) {
            call_user_func($function, $model, $subviews);
        } else {
            $model->subviews = $subviews;
        }
    }



    /**
     * @deprecated use CBView::getSubviews()
     */
    static function toSubviews($model): array {
        return CBView::getSubviews($model);
    }



    /**
     * The function performs a view tree walk. The callback is not called for
     * the root $view parameter.
     *
     * @param model $view
     * @param callable $callback
     *
     *      function callback($subview, $index, $view)
     *
     *      $subview: the view model
     *      $index: the subview's index in the subview's parent's subview array
     *      $view: the subview's parent's model
     *
     * @return void
     */
    static function walkSubviews($view, callable $callback): void {
        $subviews = CBView::getSubviews($view);

        foreach($subviews as $index => $subview) {
            call_user_func($callback, $subview, $index, $view);

            CBView::walkSubviews($subview, $callback);
        }
    }

}

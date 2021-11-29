<?php

final class
CBView {

    /**
     * Tests can set this variable to true which will make view rendering rethrow
     * exceptions rather than rendering and reporting them.
     */
    static $testModeIsActive = false;



    /* -- CBHTMLOutput interfaces -- -- -- -- -- */



    /**
     * @return [string]
     */
    static function CBHTMLOutput_JavaScriptURLs(): array {
        return [
            Colby::flexpath(__CLASS__, 'v652.js', cbsysurl()),
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
     * @NOTE 2019_12_05
     *
     *      Topic: throwing exceptions during view rendering.
     *
     *      Today I was forced to face the fact that my theories regarding
     *      exceptions during view rendering were not sturdy, because some
     *      issues rose.
     *
     *      There was a test that used every class name in the system, created
     *      an empty model with it, and then rendered it as a view. I have long
     *      had doubts about the exact purpose of this test, but I supposed it
     *      was to assert that rendering any model should not throw an
     *      exception, even though that is a bizarre way of asserting that fact.
     *      This test has been removed.
     *
     *      So I did some deep thinking, and did come to the conclusion that an
     *      exception during view rendering should not stop the entire page from
     *      rendering. However, I felt just as strongly that we needed to be
     *      sure to not hide that exception, because such an exception is an
     *      indication to developers that a bug exists.
     *
     *      If view classes are allowed to throw exceptions during
     *      CBView_render() and views aren't supposed to stop render, that means
     *      we nees a try/catch block.
     *
     *      For the moment, that is the approach we're taking and both
     *      CBView::render() and CBView::renderSpec() implement it.
     *
     *      As further information is gained, update this comment.
     *
     * @param object $viewModel
     *
     * @return void
     */
    static function render(
        stdClass $viewModel
    ): void {
        try {
            $viewClassName = CBModel::valueAsName(
                $viewModel,
                'className'
            );

            if (empty($viewClassName)) {
                throw new CBExceptionWithValue(
                    'This view model has an invalid class name.',
                    $viewModel,
                    'd96bf5026de5b05eb1a721da95ea8decf11dcd04'
                );
            }

            CBHTMLOutput::requireClassName($viewClassName);

            $function = "{$viewClassName}::CBView_render";

            if (!is_callable($function)) {
                throw new CBExceptionWithValue(
                    (
                        'The class for this view model has not ' .
                        'implemented CBView_render().'
                    ),
                    $viewModel,
                    'c69e407d30d625f060cf0589a4991531f59c6561'
                );
            }

            call_user_func(
                $function,
                $viewModel
            );
        } catch (
            Throwable $throwable
        ) {
            if (CBView::$testModeIsActive) {
                throw $throwable;
            }

            $newThrowable = new CBExceptionWithValue(
                CBConvert::stringToCleanLine(<<<EOT

                    An exception was thrown when rendering a view with this
                    model.

                EOT),
                $viewModel,
                'e5feaef4c2a106287e8982bb6a7e70b276555fe8',
                0,
                $throwable
            );

            CBErrorHandler::report(
                $newThrowable
            );

            CBView::renderViewElementForException(
                $newThrowable,
                $viewModel
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
     * @see CBView::render() for more details about view rendering.
     *
     * @param object $viewSpec
     *
     * @return void
     */
    static function
    renderSpec(
        stdClass $viewSpec
    ): void {
        try {
            $viewModel = CBModel::build($viewSpec);

            CBView::render($viewModel);
        } catch (Throwable $throwable) {
            if (CBView::$testModeIsActive) {
                throw $throwable;
            }

            CBErrorHandler::report($throwable);

            CBErrorHandler::report(
                new CBExceptionWithValue(
                    (
                        'An exception was thrown when rendering ' .
                        'a view with this spec.'
                    ),
                    $viewSpec,
                    'da871db8b36e6fb4f1ec74f5abaf24f8ccf8aac4'
                )
            );

            CBView::renderViewElementForException(
                $throwable,
                $viewSpec
            );
        }
    }
    /* renderSpec() */



    /**
     * When a view throws an exception during rendering this function is called
     * to render a proxy element so that page rendering is not cancelled.
     *
     * @param Throwable $throwable
     * @param object $viewModel
     *
     *      This value will be a view model if the exception was thrown in
     *      CBView::render(). It will be a view spec if the exception was thrown
     *      in CBView::renderSpec() while building the spec. If this parameter
     *      is an object with a valid className property, the class name
     *      "<className>_error" will be added to the element.
     *
     * @return void
     */
    private static function
    renderViewElementForException(
        Throwable $throwable,
        stdClass $viewModel
    ): void {
        ?>

        <div class="CBView_error">

            <?php


            if (
                CBUserGroup::currentUserIsMemberOfUserGroup(
                    'CBDevelopersUserGroup'
                )
            ) {
                CBExceptionView::pushThrowable(
                    $throwable
                );

                CBView::renderSpec(
                    (object)[
                        'className' => 'CBExceptionView',
                    ],
                );

                CBExceptionView::popThrowable();

                /*
                $messageAsHTML = cbhtml(
                    $throwable->getMessage()
                );

                echo "<!-- {$messageAsHTML} (see log for details) -->";*/
            }

            ?>

        </div>

        <?php
    }
    /* renderViewElementForException() */



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
    /* getSubviews() */



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
    /* setSubviews() */



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

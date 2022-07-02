<?php

/**
 * This class provides assistance for rendering HTML pages.
 *
 * Features:
 *
 *  -   Graceful exception handling
 *
 *  -   Out-of-order page composition in the sense that JavaScript files, CSS
 *      files, and JavaScript snippets can be added any time before the page is
 *      rendered. The page title and description can be set and reset any time
 *      before the page is rendered.
 *
 *  -   Duplicate JavaScript and CSS includes are automatically detected so that
 *      code can include JavaScript and CSS without having to worry about
 *      whether other code already included it.
 *
 *  -   Support for exporting values to the JavaScript environment of the page.
 *
 * Dependencies and class names:
 *
 *      Dependencies are added to an HTML page by calling
 *      CBHTMLOutput::requireClassName(). Classes that are added as dependencies
 *      can have their own dependencies which they express by implementing the
 *      following optional interfaces.
 *
 *      CBHTMLOutput_CSSURLs -> [string]
 *          A list of CSS URLs that should be loaded.
 *
 *      CBHTMLOutput_JavaScriptURLs -> [string]
 *          A list of JavaScript URLs that should be loaded.
 *
 *      CBHTMLOutput_JavaScriptVariables -> [[name, value]]
 *          A list of global JavaScript variables that should be declared.
 *
 *      CBHTMLOutput_requiredClassNames -> [string]
 *          A list of class names that are required.
 *
 * @TODO 2018_11_02
 *
 *      There are scenarios, such as generating email HTML content, when it is
 *      necessary to generate another HTML document, one that won't be
 *      immediately viewed, while handling a request. Future changes to this
 *      class should be made to enable at least the easy use of functions on
 *      this class in that scenario or even full support for that scenario.
 */
final class
CBHTMLOutput {

    const JSAsync           = 1; // 1 << 0
    const JSInHeadElement   = 2; // 1 << 1
    const JSDefer           = 4; // 1 << 2

    private static $CSSURLs;
    private static $exportedLists;
    private static $exportedVariables;
    private static $isActive = false;
    private static $javaScriptSnippetFilenames;
    private static $javaScriptSnippetStrings;
    private static $javaScriptURLs;
    private static $javaScriptURLs_immediate;
    private static $javaScriptURLsForRequiredClasses;
    private static $pageInformation;
    private static $requiredClassNames;
    private static $styleSheets;



    // -- CBCodeAdmin interfaces



    /**
     * @return object
     */
    static function
    CBCodeAdmin_searches(
    ): stdClass
    {
        $codeSearchSpec =
        CBModel::createSpec(
            'CBCodeSearch',
            '1e830951fa18c4a33e14be379670aa9122f14dc4'
        );

        CBCodeSearch::setNoticeVersion(
            $codeSearchSpec,
            '2019_07_23_1655863866'
        );

        CBCodeSearch::setWarningVersion(
            $codeSearchSpec,
            '2019_07_23_1655863867'
        );

        CBCodeSearch::setErrorVersion(
            $codeSearchSpec,
            '2022_06_22_1655863924'
        );

        $codeSearchSpec->args =
        '--ignore-file=is:CBHTMLOutput.php';

        $codeSearchSpec->cbmessage =
        <<<EOT

            Use CBHTMLOutput_CSSURLs().

        EOT;

        $codeSearchSpec->regex =
        '\brequiredCSSURLs\b';

        $codeSearchSpec->severity =
        3;

        $codeSearchSpec->title =
        'requiredCSSURLs() interface';

        return $codeSearchSpec;
    }
    // CBCodeAdmin_searches()



    /* -- accessors -- */



    /**
     * @return [string]
     */
    static function
    getSelectedMenuItemNamesArray(
    ): array {
        return CBModel::valueToArray(
            CBHTMLOutput::$pageInformation,
            'selectedMenuItemNames'
        );
    }
    /* getSelectedMenuItemNamesArray() */



    /**
     * @return [string]
     */
    static function
    setSelectedMenuItemNamesArray(
        $value
    ): void {
        CBHTMLOutput::$pageInformation->selectedMenuItemNames = $value;
    }
    /* getSelectedMenuItemNamesArray() */



    /* -- functions -- */



    /**
     * @param string|empty $CSSURL
     *
     * @return null
     */
    static function addCSSURL($CSSURL) {
        if (!empty($CSSURL) && !in_array($CSSURL, CBHTMLOutput::$CSSURLs)) {
            CBHTMLOutput::$CSSURLs[] = $CSSURL;
        }
    }



    /**
     * @param string $styleSheet
     *
     * @return null
     */
    static function addCSS($styleSheet) {
        if (!empty($styleSheet)) {
            CBHTMLOutput::$styleSheets[] = $styleSheet;
        }
    }



    /**
     * @return null
     */
    static function addJavaScriptSnippet($javaScriptSnippetFilename) {
        if (
            !in_array(
                $javaScriptSnippetFilename,
                CBHTMLOutput::$javaScriptSnippetFilenames
            )
        ) {
            CBHTMLOutput::$javaScriptSnippetFilenames[] =
            $javaScriptSnippetFilename;
        }
    }



    /**
     * @return null
     */
    static function addJavaScriptSnippetString($snippetString) {
        CBHTMLOutput::$javaScriptSnippetStrings[] = $snippetString;
    }



    /**
     * @return null
     */
    static function
    addJavaScriptURL(
        $javaScriptURL,
        $options = 0
    ) {
        /**
         * The options parameter used to be a boolean parameter indicating
         * whether the JavaScript was asynchronous or not.
         */

        if (true === $options) {
            $backtrace = debug_backtrace();

            error_log(
                'Use of a boolean parameter with the'
                . ' `CBHTMLOutput::addJavaScriptURL()` method has been'
                . " deprecated. Used in {$backtrace[0]['file']} on line"
                . " {$backtrace[0]['line']}"
            );

            $options = CBHTMLOutput::JSAsync;
        }

        CBHTMLOutput::$javaScriptURLs[$javaScriptURL] = $options;
    }
    /* addJavaScriptURL() */



    /**
     * @return null
     */
    private static function
    addJavaScriptURLForRequiredClass(
        $javaScriptURL
    ) {
        CBHTMLOutput::$javaScriptURLsForRequiredClasses[$javaScriptURL] = 0;
    }
    /* addJavaScriptURLForRequiredClass() */



    /**
     * @param string $javaScriptURL
     *
     * @return void
     */
    private static function
    addJavaScriptURL_Immediate(
        $javaScriptURL
    ): void {
        array_push(
            CBHTMLOutput::$javaScriptURLs_immediate,
            $javaScriptURL
        );
    }
    /* addJavaScriptURL_Immediate() */



    /**
     * @return null
     */
    static function addPinterest() {
        CBHTMLOutput::addJavaScriptURL(
            '//assets.pinterest.com/js/pinit.js',
            CBHTMLOutput::JSAsync | CBHTMLOutput::JSDefer
        );
    }
    /* addPinterest() */



    /**
     * Call this function before you start rendering the content of the body
     * element. When you are finished, call CBHTMLOutput::render().
     *
     * @TODO 2019_07_06, 2019_12_09
     *
     *      CBHTMLOutput may move away from begin(), reset(), and render() to a
     *      function where the caller passes a render callback as a parameter.
     *
     * @return void
     */
    static function begin(): void {
        static $backtrace = null;

        /**
         * Set this variable to true to help debug situations where this
         * function is being called twice.
         */
        $backtraceIsEnabled = false;

        if (CBHTMLOutput::$isActive) {
            $backtraceAsJSONAsMessage = CBMessageMarkup::stringToMessage(
                CBConvert::valueToPrettyJSON($backtrace)
            );

            $message = 'CBHTMLOutput::begin() has already been called.';

            $extendedMessage = <<<EOT

                {$message}

                --- pre\n{$backtraceAsJSONAsMessage}
                ---

            EOT;

            throw new CBException(
                $message,
                $extendedMessage,
                '29ade9f6d4a763a69f69c79a191893743ef3fcef'
            );
        }

        if ($backtraceIsEnabled) {
            $backtrace = debug_backtrace();
        }

        ob_start();

        set_exception_handler('CBHTMLOutput::handleException');

        CBHTMLOutput::$isActive = true;
    }
    /* begin() */



    /**
     * @return null
     */
    static function exportConstant($name) {
        CBHTMLOutput::exportVariable($name, constant($name));
    }



    /**
     * @return void
     */
    static function exportListItem(
        $listName,
        $itemKey,
        $itemValue
    ): void {
        $itemValue = json_encode($itemValue, JSON_THROW_ON_ERROR);

        if (!isset(CBHTMLOutput::$exportedLists[$listName])) {
            CBHTMLOutput::$exportedLists[$listName] = new ArrayObject();
        }

        $listObject = CBHTMLOutput::$exportedLists[$listName];
        $listObject->offsetSet($itemKey, $itemValue);
    }
    /* exportListItem() */



    /**
     * @return void
     */
    static function exportVariable(
        $name,
        $value
    ): void {
        $value = json_encode($value, JSON_THROW_ON_ERROR);

        CBHTMLOutput::$exportedVariables[$name] = $value;
    }
    /* exportVariable() */



    /**
     * @TODO 2020_12_16
     *
     *      Before this function was created, all page information was set onto
     *      the object returned by CBHTMLOutput::pageInformation(). By current
     *      standards, that is not a good way of doing things.
     *
     *      All properties allowed on the CBHTMLOutput::pageInformation() object
     *      should have get and set functions added to this class and then
     *      CBHTMLOutput::pageInformation() should be removed.
     *
     * @return ?string
     *
     *      This function returns the current class name of the class to be used
     *      for page settings if one has been set; otherwise null.
     */
    static function
    getClassNameForPageSettings(
    ): ?string {
        $classNameForPageSettings = CBModel::valueAsName(
            CBHTMLOutput::$pageInformation,
            'classNameForPageSettings'
        );

        if (
            $classNameForPageSettings !== null
        ) {
            /**
             * If a page settings class has been deprecated it can implement the
             * CBHTMLOutput_replacementClassNameForPageSettings() interface to
             * specify the page settings class that is replacing it.
             */

            $functionName = (
                $classNameForPageSettings .
                "::CBHTMLOutput_replacementClassNameForPageSettings"
            );

            if (
                is_callable($functionName)
            ) {
                $classNameForPageSettings = call_user_func(
                    $functionName
                );
            }
        }

        return $classNameForPageSettings;
    }
    /* getClassNameForPageSettings() */



    /**
     * @return bool
     */
    static function getIsActive(): bool {
        return CBHTMLOutput::$isActive;
    }
    /* getIsActive() */



    /**
     * This function is set as the exception handler in CBHTMLOutput::begin().
     * The previous exception handler is restored in CBHTMLOutput::reset().
     *
     * @param Throwable $error
     *
     * @return void
     */
    static function handleException(
        Throwable $error
    ): void {
        try {
            CBErrorHandler::report(
                $error
            );

            $classNameForPageSettings = (
                CBHTMLOutput::getClassNameForPageSettings()
            ) ?? '';

            /**
             * A page may have already been partially rendered so reset
             * CBHTMLOutput to clear the output buffer.
             */
            CBHTMLOutput::reset();

            /**
             * @TODO 2019_12_09
             *
             *      CBPageSettings is an odd place to call to render an error
             *      page. In the future investigate this and either change or
             *      document why it is okay.
             */
            CBPageSettings::renderErrorPage(
                $classNameForPageSettings,
                $error
            );
        } catch (Throwable $innerThrowable) {
            CBErrorHandler::report($innerThrowable);
        }
    }
    /* handleException() */



    /**
     * Set properties on this object to be shared by render participants of a
     * page.
     *
     * @return object
     */
    static function pageInformation() {
        return CBHTMLOutput::$pageInformation;
    }



    /**
     * @return null
     */
    private static function
    processRequiredClassNames() {
        $requiredClassNames = array_keys(
            CBHTMLOutput::$requiredClassNames
        );

        $resolvedClassNames = (
            CBRequiredClassNamesResolver::resolveRequiredClassNames(
                $requiredClassNames,
                [
                    'CBHTMLOutput_requiredClassNames',
                ]
            )
        );

        foreach (
            $resolvedClassNames as $className
        ) {
            $functionName =
            "{$className}::CBHTMLOutput_CSSURLs";

            if (
                is_callable($functionName)
            ) {
                $interfaceReturnValue =
                call_user_func(
                    $functionName
                );

                if (
                    is_array($interfaceReturnValue)
                ) {
                    foreach (
                        $interfaceReturnValue as
                        $CSSURL
                    ) {
                        CBHTMLOutput::addCSSURL(
                            $CSSURL
                        );
                    }
                }

                else
                {
                    CBHTMLOutput::addCSSURL(
                        $interfaceReturnValue
                    );
                }
            }


            /* CBHTMLOutput_JavaScriptURLs_Immediate */


            $callable = CBConvert::classNameAndFunctionNameToCallable(
                $className,
                'CBHTMLOutput_JavaScriptURLs_Immediate'
            );


            if (
                $callable !== null
            ) {
                $immediateJavaScriptURLs = call_user_func(
                    $callable
                );

                foreach(
                    $immediateJavaScriptURLs as $immediateJavaScriptURL
                ) {
                    CBHTMLOutput::addJavaScriptURL_Immediate(
                        $immediateJavaScriptURL
                    );
                };
            }


            /* CBHTMLOutput_JavaScriptURLs */

            $functionName = "{$className}::CBHTMLOutput_JavaScriptURLs";

            if (
                is_callable($functionName)
            ) {
                $javaScriptURLs = call_user_func(
                    $functionName
                );

                foreach(
                    $javaScriptURLs as $javaScriptURL
                ) {
                    CBHTMLOutput::addJavaScriptURLForRequiredClass(
                        $javaScriptURL
                    );
                };
            }


            /* CBHTMLOutput_JavaScriptVariables */

            $functionName = "{$className}::CBHTMLOutput_JavaScriptVariables";

            if (is_callable($functionName)) {
                $variables = call_user_func($functionName);

                array_walk(
                    $variables,
                    function ($variable, $index) {
                        if (is_array($variable) && count($variable) > 1) {
                            CBHTMLOutput::exportVariable(
                                $variable[0],
                                $variable[1]
                            );
                        } else {
                            $valueAsJSONAsMessage = (
                                CBMessageMarkup::stringToMessage(
                                    CBConvert::valueToPrettyJSON($variable)
                                )
                            );

                            $message = <<<EOT

                                Each element in the array returned from a
                                CBHTMLOutput_JavaScriptVariables()
                                implementation should be another array with more
                                than one element.

                                Index {$index} has the value:

                                --- pre\n{$valueAsJSONAsMessage}
                                ---

                            EOT;

                            throw new CBException(
                                'A CBHTMLOutput_JavaScriptVariables() ' .
                                'implementation returned a bad value.',
                                $message
                            );
                        }
                    }
                );
                /* array_walk() */
            }
        }
        /* foreach */
    }
    /* processRequiredClassNames() */



    /**
     * @return void
     */
    static function
    render(
    ): void {
        $bodyContent = ob_get_clean();

        ob_start();

        /**
         * If a root page settings class name has been specified, this variable
         * will contain the root page settings class name along with all if the
         * class names that it and it's required classes require.
         */
        $requiredPageSettingsClassNames = [];

        $rootPageSettingsClassName = (
            CBHTMLOutput::getClassNameForPageSettings()
        );

        if ($rootPageSettingsClassName !== null) {
            $requiredPageSettingsClassNames = (
                CBPageSettings::requiredClassNames(
                    [
                        $rootPageSettingsClassName,
                    ]
                )
            );
        }

        $htmlElementClassNames = CBPageSettings::htmlElementClassNames(
            $requiredPageSettingsClassNames
        );

        array_walk(
            $htmlElementClassNames,
            'CBHTMLOutput::requireClassName'
        );

        CBHTMLOutput::processRequiredClassNames();

        $info = CBHTMLOutput::$pageInformation;

        $title = CBModel::valueToString(
            $info,
            'title'
        );

        $description = CBModel::valueToString(
            $info,
            'description'
        );

        ?>

        <!doctype html>
        <html lang="en" class="<?= cbhtml(
            implode(
                ' ',
                $htmlElementClassNames
            )
        ) ?>">
            <head>
                <meta charset="UTF-8">
                <title><?= cbhtml($title) ?></title>
                <meta name="description" content="<?= cbhtml($description) ?>">

                <?php

                $imageForIcon = CBSitePreferences::imageForIcon();

                if (!empty($imageForIcon)) {
                    $basename = "rw320.{$imageForIcon->extension}";
                    $iconURL = CBDataStore::flexpath(
                        $imageForIcon->ID,
                        $basename,
                        cbsiteurl()
                    );

                    ?>
                    <link rel="icon" sizes="320x320" href="<?= $iconURL ?>">
                    <?php
                }

                CBPageSettings::renderHeadElementHTML(
                    $requiredPageSettingsClassNames
                );

                CBHTMLOutput::renderJavaScriptInHead();
                CBHTMLOutput::renderCSSLinks();
                CBHTMLOutput::renderStyleSheets();

                ?>
            </head>
            <body>
                <?php

                CBPageSettings::renderPreContentHTML(
                    $requiredPageSettingsClassNames
                );

                echo $bodyContent;

                $bodyContent = null;

                CBPageSettings::renderPostContentHTML(
                    $requiredPageSettingsClassNames
                );

                CBHTMLOutput::renderJavaScript();

                ?>
            </body>
        </html>

        <?php

        ob_flush();

        CBHTMLOutput::reset();
    }
    /* render() */



    /**
     * @TODO 2019_08_04
     *
     *      THIS FUNCTION IS UNDER DEVELOPMENT, DO NOT USE OUTSIDE OF
     *      DEVELOPMENT OR TESTING SCENARIOS
     *
     * This function will never throw an exception.
     *
     * @return void
     */
    static function render2(
        callable $renderCallback
    ): void {
        if (
            CBUserGroup::currentUserIsMemberOfUserGroup(
                'CBDevelopersUserGroup'
            )
        ) {
            return;
        }

        CBHTMLOutput::begin();

        try {
            call_user_func($renderCallback);

            CBHTMLOutput::render();
        } catch (Throwable $throwable) {
            CBErrorHandler::report($throwable);

            CBHTMLOutput::reset();

            CBPageSettings::renderErrorPage(
                CBHTMLOutput::getClassNameForPageSettings() ?? '',
                $throwable
            );
        }
    }
    /* render2() */



    /**
     * This function can be called at almost any time, even by a view in the
     * middle of a rendering pass to render a 404 page and exit the process.
     *
     * An example would be a view that uses a query variable to help render its
     * content. If the query variable holds an invalid value, the view would
     * call this method to force a 404 response.
     *
     * One time when it's not appropriate to call this function is when handling
     * an Ajax request.
     *
     * @TODO There should be better error handling than this during a rendering
     * pass, so in the future this will change.
     *
     * @return null
     */
    static function render404() {
        CBHTMLOutput::reset();

        include Colby::findFile('handlers/handle-default.php');

        exit;
    }



    /**
     * @return null
     */
    private static function renderCSSLinks() {
        foreach (CBHTMLOutput::$CSSURLs as $URL) {
            echo '<link rel="stylesheet" href="' . $URL . '">';
        }
    }



    /**
     * @return void
     */
    private static function
    renderJavaScript(
    ): void {
        foreach (
            CBHTMLOutput::$javaScriptURLsForRequiredClasses as $URL => $options
        ) {
            echo "<script src=\"{$URL}\"></script>";
        }

        foreach (
            CBHTMLOutput::$javaScriptURLs as $URL => $options
        ) {
            if (isset(CBHTMLOutput::$javaScriptURLsForRequiredClasses[$URL])) {
                continue;
            }

            if (!($options & CBHTMLOutput::JSInHeadElement)) {
                $async = $options & CBHTMLOutput::JSAsync ? 'async ' : '';
                $defer = $options & CBHTMLOutput::JSDefer ? 'defer ' : '';

                echo (
                    '<script ' .
                    $async .
                    $defer .
                    'src="' .
                    $URL .
                    '"></script>'
                );
            }
        }

        foreach (
            CBHTMLOutput::$javaScriptSnippetFilenames as $snippetFilename
        ) {
            include $snippetFilename;
        }

        $snippetStrings = CBHTMLOutput::$javaScriptSnippetStrings;

        if (!empty($snippetStrings)) {
            foreach ($snippetStrings as $snippetString) {
                echo <<<EOT

                    <script>
                    use strict;
                    {$snippetString}
                    </script>

                EOT;
            }
        }
    }
    /* renderJavaScript() */



    /**
     * @return void
     */
    private static function
    renderJavaScriptInHead(
    ): void {
        if (
            !empty(CBHTMLOutput::$exportedVariables) ||
            !empty(CBHTMLOutput::$exportedLists)
        ) {
            echo "<script>\n\"use strict\";\n";

            foreach (CBHTMLOutput::$exportedVariables as $name => $value) {
                echo "var {$name} = {$value};\n";
            }

            foreach (CBHTMLOutput::$exportedLists as $name => $list) {
                echo "var {$name} = {};\n";

                foreach ($list as $key => $value) {
                    echo "{$name}[\"{$key}\"] = {$value};\n";
                }
            }

            echo "</script>\n";
        }

        foreach (
            CBHTMLOutput::$javaScriptURLs as $javaScriptURL => $options
        ) {
            if (
                $options & CBHTMLOutput::JSInHeadElement
            ) {
                $async = $options & CBHTMLOutput::JSAsync ? 'async ' : '';
                $defer = $options & CBHTMLOutput::JSDefer ? 'defer ' : '';

                $javaScriptURLAsHTML = cbhtml(
                    $javaScriptURL
                );

                echo CBConvert::stringToCleanLine(<<<EOT

                    <script
                        src="{$javaScriptURLAsHTML}"
                        {$async} {$defer}
                    >
                    </script>

                EOT);
            }
        }

        foreach(
            CBHTMLOutput::$javaScriptURLs_immediate as $javaScriptURL
        ) {
            $javaScriptURLAsHTML = cbhtml(
                $javaScriptURL
            );

            echo CBConvert::stringToCleanLine(<<<EOT

                <script
                    src="{$javaScriptURLAsHTML}"
                >
                </script>

            EOT);

        }
    }
    /* renderJavaScriptInHead() */



    /**
     * @return void
     */
    private static function renderStyleSheets(): void {
        array_walk(CBHTMLOutput::$styleSheets, function ($styleSheet) {
            echo "<style>$styleSheet</style>";
        });
    }



    /**
     * @return void
     */
    static function requireClassName($className): void {
        if (!array_key_exists($className, CBHTMLOutput::$requiredClassNames)) {
            CBHTMLOutput::$requiredClassNames[$className] = true;
        }
    }



    /**
     * @return void
     */
    static function
    reset(
    ): void {
        if (CBHTMLOutput::$isActive) {
            restore_exception_handler();
            ob_end_clean();
        }

        CBHTMLOutput::$CSSURLs = array();
        CBHTMLOutput::$exportedLists = array();
        CBHTMLOutput::$exportedVariables = array();
        CBHTMLOutput::$isActive = false;
        CBHTMLOutput::$javaScriptSnippetFilenames = array();
        CBHTMLOutput::$javaScriptSnippetStrings = array();
        CBHTMLOutput::$javaScriptURLs = [];
        CBHTMLOutput::$javaScriptURLs_immediate = [];
        CBHTMLOutput::$javaScriptURLsForRequiredClasses = [];
        CBHTMLOutput::$pageInformation = (object)[];
        CBHTMLOutput::$requiredClassNames = [];
        CBHTMLOutput::$styleSheets = [];

        /**
         * @TODO 2017_08_02
         *
         *      Colby was added by default to requiredClassNames to smooth the
         *      transition of moving the Colby JavaScript and CSS files into the
         *      classes/Colby directory. Now, if you need these files for a
         *      view, layout, or whatever, you should add Colby to your list of
         *      required class names.
         */

        CBHTMLOutput::requireClassName('Colby');
    }
    /* reset() */

}
/* CBHTMLOutput */

CBHTMLOutput::reset();

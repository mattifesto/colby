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
final class CBHTMLOutput {

    const JSAsync           = 1; // 1 << 0
    const JSInHeadElement   = 2; // 1 << 1
    const JSDefer           = 4; // 1 << 2

    /**
     * @deprecated
     *
     * Set:
     *  CBHTMLOutput::pageInformation()->classNameForPageSettings = 'MyClass';
     *
     * Get:
     *  CBHTMLOutput::classNameForPageSettings();
     */
    public static $classNameForSettings;

    private static $CSSURLs;
    private static $exportedLists;
    private static $exportedVariables;
    private static $isActive = false;
    private static $javaScriptSnippetFilenames;
    private static $javaScriptSnippetStrings;
    private static $javaScriptURLs;
    private static $javaScriptURLsForRequiredClasses;
    private static $pageInformation;
    private static $requiredClassNames;
    private static $styleSheets;

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
        if (!in_array($javaScriptSnippetFilename, CBHTMLOutput::$javaScriptSnippetFilenames)) {
            CBHTMLOutput::$javaScriptSnippetFilenames[] = $javaScriptSnippetFilename;
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
    static function addJavaScriptURL($javaScriptURL, $options = 0) {
        /**
         * The options parameter used to be a boolean parameter indicating
         * whether the JavaScript was asynchronous or not.
         */

        if (true === $options) {
            $backtrace = debug_backtrace();

            error_log("Use of a boolean parameter with the `CBHTMLOutput::addJavaScriptURL` method has been deprecated. Used in {$backtrace[0]['file']} on line {$backtrace[0]['line']}");

            $options = CBHTMLOutput::JSAsync;
        }

        CBHTMLOutput::$javaScriptURLs[$javaScriptURL] = $options;
    }

    /**
     * @return null
     */
    private static function addJavaScriptURLForRequiredClass($javaScriptURL) {
        CBHTMLOutput::$javaScriptURLsForRequiredClasses[$javaScriptURL] = 0;
    }

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
     * @return void
     */
    static function begin(): void {
        if (CBHTMLOutput::$isActive) {
            throw new CBException(
                'CBHTMLOutput buffering has already begun.',
                '',
                '29ade9f6d4a763a69f69c79a191893743ef3fcef'
            );
        }

        ob_start();

        set_exception_handler('CBHTMLOutput::handleException');

        CBHTMLOutput::$isActive = true;
    }

    /**
     * @deprecated 2018.04.12
     *
     *      This function will be removed because the only supported way of
     *      specifying page settings is to set the classNameForPageSettings
     *      property on CBHTMLOutput::pageInformation().
     *
     * This function calculates the current class name of the class to be used
     * for page settings.
     *
     * @return ?string
     */
    static function classNameForPageSettings(): ?string {
        $className = CBHTMLOutput::$classNameForSettings; /* deprecated */

        if (empty($className)) {
            $className = CBConvert::valueToString(
                CBModel::value(CBHTMLOutput::$pageInformation, 'classNameForPageSettings')
            );
        }

        if (empty($className)) {
            $className = CBPageSettings::defaultClassName(); /* deprecated */
        }

        return $className;
    }

    /**
     * @return null
     */
    static function exportConstant($name) {
        CBHTMLOutput::exportVariable($name, constant($name));
    }

    /**
     * @return null
     */
    static function exportListItem($listName, $itemKey, $itemValue) {
        $itemValue = json_encode($itemValue);

        if (!isset(CBHTMLOutput::$exportedLists[$listName])) {
            CBHTMLOutput::$exportedLists[$listName] = new ArrayObject();
        }

        $listObject = CBHTMLOutput::$exportedLists[$listName];
        $listObject->offsetSet($itemKey, $itemValue);
    }

    /**
     * @return null
     */
    static function exportVariable($name, $value) {
        $value = json_encode($value);

        CBHTMLOutput::$exportedVariables[$name] = $value;
    }

    /**
     * 2017.12.03 This function was updated to comply with the custom exception
     * handler documentation in the CBErrorHandler::handle() comments.
     *
     * @param Throwable $exception
     *
     * @return null
     */
    static function handleException(Throwable $throwable) {
        CBErrorHandler::report($throwable);

        try {
            $classNameForPageSettings =
            CBHTMLOutput::classNameForPageSettings();

            /**
             * A page may have already been partially rendered so reset
             * CBHTMLOutput to clear the output buffer.
             */
            CBHTMLOutput::reset();

            CBPageSettings::renderErrorPage(
                $classNameForPageSettings,
                $throwable
            );
        } catch (Throwable $innerThrowable) {
            /* this is a severe situation, just try to report and exit */
            CBErrorHandler::report($innerThrowable);
            exit;
        }
    }

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
    private static function processRequiredClassNames() {
        $requiredClassNames = array_keys(CBHTMLOutput::$requiredClassNames);
        $resolvedClassNames = CBRequiredClassNamesResolver::resolveRequiredClassNames(
            $requiredClassNames,
            [
                'CBHTMLOutput_requiredClassNames',
                'requiredClassNames', /* deprecated */
            ]
        );

        foreach ($resolvedClassNames as $className) {
            if (
                is_callable($function = "{$className}::CBHTMLOutput_CSSURLs") ||
                is_callable($function = "{$className}::requiredCSSURLs")
            ) {
                $URLs = call_user_func($function);
                array_walk($URLs, function ($URL) { CBHTMLOutput::addCSSURL($URL); });
            }

            if (
                is_callable($function = "{$className}::CBHTMLOutput_JavaScriptURLs") ||
                is_callable($function = "{$className}::requiredJavaScriptURLs")
            ) {
                $URLs = call_user_func($function);
                array_walk($URLs, function ($URL) { CBHTMLOutput::addJavaScriptURLForRequiredClass($URL); });
            }

            if (
                is_callable($function = "{$className}::CBHTMLOutput_JavaScriptVariables") ||
                is_callable($function = "{$className}::requiredJavaScriptVariables")
            ) {
                $variables = call_user_func($function);
                array_walk($variables, function ($variable, $index) use ($function) {
                    if (is_array($variable) && count($variable) > 1) {
                        CBHTMLOutput::exportVariable($variable[0], $variable[1]);
                    } else {
                        $valueAsJSONAsMessage = CBMessageMarkup::stringToMessage(
                            CBConvert::valueToPrettyJSON($variable)
                        );

                        $message = <<<EOT

                            Each element in the array returned from a
                            CBHTMLOutput_JavaScriptVariables() implementation
                            should be another array with more than one element.

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
                });
            }
        }
    }
    /* processRequiredClassNames() */


    /**
     * @return void
     */
    static function render(): void {
        $bodyContent = ob_get_clean();

        ob_start();

        if ($className = CBHTMLOutput::classNameForPageSettings()) {
            $pageSettingsClassNames = CBPageSettings::requiredClassNames([$className]);
        } else {
            $pageSettingsClassNames = [];
        }

        $htmlElementClassNames = CBPageSettings::htmlElementClassNames($pageSettingsClassNames);

        array_walk($htmlElementClassNames, 'CBHTMLOutput::requireClassName');

        CBHTMLOutput::processRequiredClassNames();

        $info = CBHTMLOutput::$pageInformation;
        $title = CBModel::valueToString($info, 'title');
        $description = CBModel::valueToString($info, 'description');

        ?>

        <!doctype html>
        <html lang="en" class="<?= cbhtml(implode(' ',$htmlElementClassNames)) ?>">
            <head>
                <meta charset="UTF-8">
                <title><?= cbhtml($title) ?></title>
                <meta name="description" content="<?= cbhtml($description) ?>">

                <?php

                $imageForIcon = CBSitePreferences::imageForIcon();

                if (!empty($imageForIcon)) {
                    $basename = "rw320.{$imageForIcon->extension}";
                    $iconURL = CBDataStore::flexpath($imageForIcon->ID, $basename, CBSiteURL);

                    ?>
                    <link rel="icon" sizes="320x320" href="<?= $iconURL ?>">
                    <?php
                }

                CBPageSettings::renderHeadElementHTML($pageSettingsClassNames);

                CBHTMLOutput::renderJavaScriptInHead();
                CBHTMLOutput::renderCSSLinks();
                CBHTMLOutput::renderStyleSheets();

                ?>
            </head>
            <body>
                <?php CBPageSettings::renderPreContentHTML($pageSettingsClassNames) ?>
                <?php echo $bodyContent; $bodyContent = null; ?>
                <?php CBPageSettings::renderPostContentHTML($pageSettingsClassNames) ?>
                <?php CBHTMLOutput::renderJavaScript(); ?>
            </body>
        </html>

        <?php

        ob_flush();

        CBHTMLOutput::reset();
    }
    /* render() */


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
     * @return null
     */
    private static function renderJavaScript() {
        if (!empty(CBHTMLOutput::$exportedVariables) || !empty(CBHTMLOutput::$exportedLists)) {
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

        foreach (CBHTMLOutput::$javaScriptURLsForRequiredClasses as $URL => $options) {
            echo "<script src=\"{$URL}\"></script>";
        }

        foreach (CBHTMLOutput::$javaScriptURLs as $URL => $options) {
            if (isset(CBHTMLOutput::$javaScriptURLsForRequiredClasses[$URL])) {
                continue;
            }

            if (!($options & CBHTMLOutput::JSInHeadElement)) {
                $async = $options & CBHTMLOutput::JSAsync ? 'async ' : '';
                $defer = $options & CBHTMLOutput::JSDefer ? 'defer ' : '';

                echo '<script ' . $async . $defer . 'src="' . $URL . '"></script>';
            }
        }

        foreach (CBHTMLOutput::$javaScriptSnippetFilenames as $snippetFilename) {
            include $snippetFilename;
        }

        if (!empty(CBHTMLOutput::$javaScriptSnippetStrings)) {
            foreach (CBHTMLOutput::$javaScriptSnippetStrings as $snippetString) {
                echo "\n<script>\n\"use strict\";\n\n{$snippetString}\n\n</script>\n";
            }
        }
    }

    /**
     * @return null
     */
    private static function renderJavaScriptInHead() {
        foreach (CBHTMLOutput::$javaScriptURLs as $URL => $options) {
            if ($options & CBHTMLOutput::JSInHeadElement) {
                $async = $options & CBHTMLOutput::JSAsync ? 'async ' : '';
                $defer = $options & CBHTMLOutput::JSDefer ? 'defer ' : '';

                echo '<script ' . $async . $defer . 'src="' . $URL . '"></script>';
            }
        }
    }

    /**
     * @return null
     */
    private static function renderStyleSheets() {
        array_walk(CBHTMLOutput::$styleSheets, function ($styleSheet) {
            echo "<style>$styleSheet</style>";
        });
    }

    /**
     * @return null
     */
    static function requireClassName($className) {
        if (!array_key_exists($className, CBHTMLOutput::$requiredClassNames)) {
            CBHTMLOutput::$requiredClassNames[$className] = true;
        }
    }

    /**
     * @return null
     */
    static function reset() {
        if (CBHTMLOutput::$isActive) {
            restore_exception_handler();
            ob_end_clean();
        }

        CBHTMLOutput::$classNameForSettings = null; // @deprecated
        CBHTMLOutput::$CSSURLs = array();
        CBHTMLOutput::$exportedLists = array();
        CBHTMLOutput::$exportedVariables = array();
        CBHTMLOutput::$isActive = false;
        CBHTMLOutput::$javaScriptSnippetFilenames = array();
        CBHTMLOutput::$javaScriptSnippetStrings = array();
        CBHTMLOutput::$javaScriptURLs = [];
        CBHTMLOutput::$javaScriptURLsForRequiredClasses = [];
        CBHTMLOutput::$pageInformation = (object)[];
        CBHTMLOutput::$requiredClassNames = [];
        CBHTMLOutput::$styleSheets = [];

        /**
         * @NOTE 2017.08.02 Colby was added by default to requiredClassNames to
         *       smooth the transition of moving the Colby JavaScript and CSS
         *       files into the classes/Colby directory. Now, if you need these
         *       files for a view, layout, or whatever, you should add Colby to
         *       your list of required class names.
         */

        CBHTMLOutput::requireClassName('Colby');
    }
}

CBHTMLOutput::reset();

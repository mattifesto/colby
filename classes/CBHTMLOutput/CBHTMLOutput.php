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
 */
final class CBHTMLOutput {

    const JSAsync           = 1; // 1 << 0
    const JSInHeadElement   = 2; // 1 << 1
    const JSDefer           = 4; // 1 << 2

    public static $classNameForSettings;

    private static $CSSURLs;
    private static $descriptionHTML; /* deprecated */
    private static $exportedLists;
    private static $exportedVariables;
    private static $isActive = false;
    private static $javaScriptSnippetFilenames;
    private static $javaScriptSnippetStrings;
    private static $javaScriptURLs;
    private static $requiredClassNames;
    private static $styleSheets = [];
    private static $titleHTML; /* deprecated */

    /**
     * @param string|empty $CSSURL
     *
     * @return null
     */
    static function addCSSURL($CSSURL) {
        if (!empty($CSSURL) && !in_array($CSSURL, self::$CSSURLs)) {
            CBHTMLOutput::$CSSURLs[] = $CSSURL;
        }
    }

    /**
     * @param string $styleSheet
     *
     * @return null
     */
    static function addCSS($styleSheet) {
        CBHTMLOutput::$styleSheets[] = $styleSheet;
    }

    /**
     * @return void
     */
    public static function addJavaScriptSnippet($javaScriptSnippetFilename)
    {
        if (!in_array($javaScriptSnippetFilename, self::$javaScriptSnippetFilenames))
        {
            self::$javaScriptSnippetFilenames[] = $javaScriptSnippetFilename;
        }
    }

    /**
     * @return void
     */
    public static function addJavaScriptSnippetString($snippetString)
    {
        self::$javaScriptSnippetStrings[] = $snippetString;
    }

    /**
     * @return void
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

        self::$javaScriptURLs[$javaScriptURL] = $options;
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

    /**
     * @return void
     */
    public static function begin()
    {
        ob_start();

        set_exception_handler('CBHTMLOutput::handleException');

        self::$isActive = true;
    }

    /**
     * @return void
     */
    public static function exportConstant($name)
    {
        self::exportVariable($name, constant($name));
    }

    /**
     * @return void
     */
    public static function exportListItem($listName, $itemKey, $itemValue)
    {
        $itemValue = json_encode($itemValue);

        if (!isset(self::$exportedLists[$listName]))
        {
            self::$exportedLists[$listName] = new ArrayObject();
        }

        $listObject = self::$exportedLists[$listName];
        $listObject->offsetSet($itemKey, $itemValue);
    }

    /**
     * @return void
     */
    public static function exportVariable($name, $value)
    {
        $value = json_encode($value);

        self::$exportedVariables[$name] = $value;
    }

    /**
     * @param Exception $exception
     *
     * @return null
     */
    static function handleException($exception) {
        $classNameForSettings = CBHTMLOutput::$classNameForSettings;

        // Partial page may have been rendered, clear output buffer
        CBHTMLOutput::reset();

        if (is_callable($function = "{$classNameForSettings}::renderPageForException")) {
            try {
                call_user_func($function, $exception);
                Colby::reportException($exception);
            } catch (Exception $innerException) {
                // Partial page may have been rendered, clear output buffer
                CBHTMLOutput::reset();

                // Report the exception that occured in the try block
                Colby::reportException($innerException);

                // Revert to the bare bones Colby exception handler for the
                // original exception
                Colby::handleException($exception);
            }
        } else {
            Colby::handleException($exception);
        }
    }

    /**
     * @return null
     */
    private static function processRequiredClassNames() {
        $requiredClassNames = array_keys(self::$requiredClassNames);
        $resolvedClassNames = CBRequiredClassNamesResolver::resolveRequiredClassNames($requiredClassNames);

        foreach ($resolvedClassNames as $className) {
            if (is_callable($function = "{$className}::requiredCSSURLs")) {
                $URLs = call_user_func($function);
                array_walk($URLs, function ($URL) { CBHTMLOutput::addCSSURL($URL); });
            }

            if (is_callable($function = "{$className}::requiredJavaScriptURLs")) {
                $URLs = call_user_func($function);
                array_walk($URLs, function ($URL) { CBHTMLOutput::addJavaScriptURL($URL); });
            }

            if (is_callable($function = "{$className}::requiredJavaScriptVariables")) {
                $variables = call_user_func($function);
                array_walk($variables, function ($variable) use ($function) {
                    if (is_array($variable) && count($variable) > 1) {
                        CBHTMLOutput::exportVariable($variable[0], $variable[1]);
                    } else {
                        throw new Exception("The function {$function}() returned a bad value.");
                    }
                });
            }
        }
    }

    /**
     * NOTE: 2017.01.01
     *
     *      This function include a polyfill for Promise right before the
     *      content of renderEndOfBodyContent() which generally includes
     *      "Colby.js".
     *
     *      While CBHTMLOutput has generally tried to avoid placing any dogma on
     *      pages, this effort doesn't always make sense. At this point Promise
     *      is very useful and included with all browsers except IE11.
     *      "Colby.js" now uses Promise to ease ajax requests. If there ever
     *      comes a time when this polyfill is getting in the way, feel free to
     *      reconsider. However, it is highly unlikely that will happen before
     *      we stop supporting IE11 altogether and remove the polyfill.
     *
     * @return null
     */
    public static function render() {
        $bodyContent = ob_get_clean();
        $pageContext = CBPageContext::current();
        $settingsHeadContent = '';
        $settingsStartOfBodyContent = '';
        $settingsEndOfBodyContent = '';
        $classNameForSettings = CBHTMLOutput::$classNameForSettings;
        $defaultThemeClassName = 'CBLightTheme';

        if (empty($classNameForSettings)) {
            $classNameForSettings = CBSitePreferences::defaultClassNameForPageSettings();
        }

        if (!empty($classNameForSettings)) {
            CBHTMLOutput::requireClassName($classNameForSettings);

            if (is_callable($function = "{$classNameForSettings}::defaultThemeClassName")) {
                $defaultThemeClassName = call_user_func($function);
                CBHTMLOutput::requireClassName($defaultThemeClassName);
            }
        }

        CBHTMLOutput::processRequiredClassNames();

        if (is_callable($function = "{$classNameForSettings}::renderHeadContent")) {
            ob_start();
            call_user_func($function);

            $settingsHeadContent = ob_get_clean();
        }

        if (is_callable($function = "{$classNameForSettings}::renderStartOfBodyContent")) {
            ob_start();
            call_user_func($function);

            $settingsStartOfBodyContent = ob_get_clean();
        }

        if (is_callable($function = "{$classNameForSettings}::renderEndOfBodyContent")) {
            ob_start();
            call_user_func($function);

            $settingsEndOfBodyContent = ob_get_clean();
        }

        ob_start();

        $titleAsHTML = empty($pageContext->titleAsHTML) ? self::$titleHTML : $pageContext->titleAsHTML;
        $descriptionAsHTML = empty($pageContext->descriptionAsHTML) ? self::$descriptionHTML : $pageContext->descriptionAsHTML;

        ?>

        <!doctype html>
        <html lang="en" class="<?= $defaultThemeClassName ?>">
            <head>
                <meta charset="UTF-8">
                <title><?= $titleAsHTML ?></title>
                <meta name="description" content="<?= $descriptionAsHTML ?>">

                <meta property="fb:app_id" content="<?= CBFacebookAppID ?>">
                <meta property="og:title" content="<?= $titleAsHTML ?>">
                <meta property="og:description" content="<?= $descriptionAsHTML ?>">

                <?php

                if (!empty($pageContext->imageURL)) {
                    ?>
                    <meta property="og:image" content="<?= $pageContext->imageURL ?>">
                    <?php
                }

                $imageForIcon = CBSitePreferences::imageForIcon();

                if (!empty($imageForIcon)) {
                    $basename = "rw320.{$imageForIcon->extension}";
                    $iconURL = CBDataStore::flexpath($imageForIcon->ID, $basename, CBSiteURL);

                    ?>
                    <link rel="icon" sizes="320x320" href="<?= $iconURL ?>">
                    <?php
                }

                echo $settingsHeadContent;

                CBHTMLOutput::renderJavaScriptInHead();
                CBHTMLOutput::renderCSSLinks();
                CBHTMLOutput::renderStyleSheets();

                ?>
            </head>
            <body>
                <?= $settingsStartOfBodyContent ?>
                <?php echo $bodyContent; $bodyContent = null; ?>
                <script src="<?= CBSystemURL ?>/javascript/es6-promise.auto.min.js"></script>
                <?= $settingsEndOfBodyContent ?>
                <?php self::renderJavaScript(); ?>
            </body>
        </html>

        <?php

        ob_flush();

        self::reset();
    }

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
    public static function render404() {
        self::reset();
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

        foreach (CBHTMLOutput::$javaScriptURLs as $URL => $options) {
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
    public static function requireClassName($className) {
        if (!array_key_exists($className, self::$requiredClassNames)) {
            self::$requiredClassNames[$className] = true;
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

        CBHTMLOutput::$classNameForSettings = null;
        CBHTMLOutput::$CSSURLs = array();
        CBHTMLOutput::$descriptionHTML = '';
        CBHTMLOutput::$exportedLists = array();
        CBHTMLOutput::$exportedVariables = array();
        CBHTMLOutput::$isActive = false;
        CBHTMLOutput::$javaScriptSnippetFilenames = array();
        CBHTMLOutput::$javaScriptSnippetStrings = array();
        CBHTMLOutput::$javaScriptURLs = array();
        CBHTMLOutput::$requiredClassNames = [];
        CBHTMLOutput::$styleSheets = [];
        CBHTMLOutput::$titleHTML = '';
    }

    /**
     * @deprecated use CBPageContext
     *
     * @return null
     */
    public static function setTitleHTML($titleHTML) {
        self::$titleHTML = $titleHTML;
    }

    /**
     * @deprecated use CBPageContext
     *
     * @return null
     */
    public static function setDescriptionHTML($descriptionHTML) {
        self::$descriptionHTML = $descriptionHTML;
    }
}

CBHTMLOutput::reset();

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
class CBHTMLOutput
{
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
    private static $titleHTML; /* deprecated */

    /**
     * @param string|empty $CSSURL
     *
     * @return void
     */
    public static function addCSSURL($CSSURL) {
        if (!empty($CSSURL) && !in_array($CSSURL, self::$CSSURLs)) {
            self::$CSSURLs[] = $CSSURL;
        }
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
     * @return void
     */
    public static function handleException($exception)
    {
        self::reset();

        Colby::handleException($exception);
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
                array_walk($variables, function ($variable) {
                    CBHTMLOutput::exportVariable($variable[0], $variable[1]);
                });
            }
        }
    }

    /**
     * @return void
     */
    public static function render() {
        $bodyContent = ob_get_clean();
        $pageContext = CBPageContext::current();
        $settingsHeadContent = '';
        $settingsStartOfBodyContent = '';
        $settingsEndOfBodyContent = '';
        $classNameForSettings = empty(self::$classNameForSettings) ? CBSitePreferences::defaultClassNameForPageSettings() : self::$classNameForSettings;

        if (is_callable($function = "{$classNameForSettings}::requiredClassNames")) {
            $classNames = call_user_func($function);
            array_walk($classNames, "CBHTMLOutput::requireClassName");
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

        ?>

        <!doctype html>
        <html lang="en">
            <head>
                <meta charset="UTF-8">
                <title><?= empty($pageContext->titleAsHTML) ? self::$titleHTML : $pageContext->titleAsHTML ?></title>
                <meta name="description" content="<?= empty($pageContext->descriptionAsHTML) ? self::$descriptionHTML : $pageContext->descriptionAsHTML ?>">
                <?= $settingsHeadContent ?>
                <?php self::renderJavaScriptInHead(); ?>
                <?php self::renderCSSLinks(); ?>
            </head>
            <body>
                <?= $settingsStartOfBodyContent ?>
                <?php echo $bodyContent; $bodyContent = null; ?>
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
     * @return void
     */
    private static function renderCSSLinks()
    {
        foreach (self::$CSSURLs as $URL)
        {
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
     * @return void
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
    public static function requireClassName($className) {
        if (!array_key_exists($className, self::$requiredClassNames)) {
            self::$requiredClassNames[$className] = true;
        }
    }

    /**
     * @return null
     */
    public static function reset() {
        if (self::$isActive) {
            restore_exception_handler();
            ob_end_clean();
        }

        self::$classNameForSettings = null;
        self::$CSSURLs = array();
        self::$descriptionHTML = '';
        self::$exportedLists = array();
        self::$exportedVariables = array();
        self::$isActive = false;
        self::$javaScriptSnippetFilenames = array();
        self::$javaScriptSnippetStrings = array();
        self::$javaScriptURLs = array();
        self::$requiredClassNames = [];
        self::$titleHTML = '';
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

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

    public static $classNameForSettings = '';

    private static $CSSURLs;
    private static $descriptionHTML;
    private static $exportedLists;
    private static $exportedVariables;
    private static $isActive = false;
    private static $javaScriptSnippetFilenames;
    private static $javaScriptSnippetStrings;
    private static $javaScriptURLs;
    private static $javaScriptURLsInHead;
    private static $requiredClassNames;
    private static $titleHTML;

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
    public static function addJavaScriptURL($javaScriptURL, $options = 0)
    {
        /**
         * The options parameter used to be a boolean parameter indicating
         * whether the JavaScript was asynchronous or not.
         */

        if (true === $options)
        {
            $backtrace = debug_backtrace();

            error_log("Use of a boolean parameter with the `CBHTMLOutput::addJavaScriptURL` method has been deprecated. Used in {$backtrace[0]['file']} on line {$backtrace[0]['line']}");

            $options = CBHTMLOutput::JSAsync;
        }

        if ($options & CBHTMLOutput::JSInHeadElement)
        {
            self::$javaScriptURLsInHead[$javaScriptURL] = !!($options & CBHTMLOutput::JSAsync);
        }
        else
        {
            self::$javaScriptURLs[$javaScriptURL] = !!($options & CBHTMLOutput::JSAsync);
        }
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
     * @return string|null
     */
    public static function descriptionAsHTML() {
        return self::$descriptionHTML;
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
        $resolvedClassNames = CBRequiredClassNamesResolver::resolveRequiredClassNames(self::$requiredClassNames);

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
        $bodyContent                = ob_get_clean();
        $settingsHeadContent        = '';
        $settingsStartOfBodyContent = '';
        $settingsEndOfBodyContent   = '';
        $classNameForSettings       = (self::$classNameForSettings === '') ? CBSitePreferences::defaultClassNameForPageSettings() : self::$classNameForSettings;

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
                <title><?php echo self::$titleHTML; ?></title>
                <meta name="description" content="<?php echo self::$descriptionHTML; ?>">
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
    private static function renderJavaScript()
    {
        echo "<script>\n\"use strict\";\n";

        foreach (self::$exportedVariables as $name => $value)
        {
            echo "var {$name} = {$value};\n";
        }

        foreach (self::$exportedLists as $name => $list)
        {
            echo "var {$name} = {};\n";

            foreach ($list as $key => $value)
            {
                echo "{$name}[\"{$key}\"] = {$value};\n";
            }
        }

        echo "</script>\n";

        foreach (self::$javaScriptURLs as $URL => $isAsync)
        {
            $async = $isAsync ? 'async ' : '';

            echo '<script ' . $async . 'src="' . $URL . '"></script>';
        }

        foreach (self::$javaScriptSnippetFilenames as $snippetFilename)
        {
            include $snippetFilename;
        }

        foreach (self::$javaScriptSnippetStrings as $snippetString)
        {
            echo "\n<script>\n\"use strict\";\n\n{$snippetString}\n\n</script>\n";
        }
    }

    /**
     * @return void
     */
    private static function renderJavaScriptInHead()
    {
        foreach (self::$javaScriptURLsInHead as $URL => $isAsync)
        {
            $async = $isAsync ? 'async ' : '';

            echo '<script ' . $async . 'src="' . $URL . '"></script>';
        }
    }

    /**
     * @return null
     */
    public static function requireClassName($className) {
        self::$requiredClassNames[] = $className;
    }

    /**
     * @return null
     */
    public static function reset() {
        if (self::$isActive) {
            restore_exception_handler();
            ob_end_clean();
        }

        self::$classNameForSettings         = '';
        self::$CSSURLs                      = array();
        self::$descriptionHTML              = '';
        self::$exportedLists                = array();
        self::$exportedVariables            = array();
        self::$isActive                     = false;
        self::$javaScriptSnippetFilenames   = array();
        self::$javaScriptSnippetStrings     = array();
        self::$javaScriptURLs               = array();
        self::$javaScriptURLsInHead         = array();
        self::$requiredClassNames           = [];
        self::$titleHTML                    = '';
    }

    /**
     * @return void
     */
    public static function setTitleHTML($titleHTML)
    {
        self::$titleHTML = $titleHTML;
    }

    /**
     * @return void
     */
    public static function setDescriptionHTML($descriptionHTML)
    {
        self::$descriptionHTML = $descriptionHTML;
    }

    /**
     * @return string|null
     */
    public static function titleAsHTML() {
        return self::$titleHTML;
    }
}

CBHTMLOutput::reset();

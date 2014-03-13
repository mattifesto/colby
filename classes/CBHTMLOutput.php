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
    private static $CSSURLs;
    private static $descriptionHTML;
    private static $exportedLists;
    private static $exportedVariables;
    private static $isActive;
    private static $javaScriptSnippets;
    private static $javaScriptURLs;
    private static $titleHTML;

    /**
     * @return void
     */
    public static function addCSSURL($CSSURL)
    {
        if (!in_array($CSSURL, self::$CSSURLs))
        {
            self::$CSSURLs[] = $CSSURL;
        }
    }

    /**
     * @return void
     */
    public static function addJavaScriptSnippet($javaScriptSnippet)
    {
        if (!in_array($javaScriptSnippet, self::$javaScriptSnippets))
        {
            self::$javaScriptSnippets[] = $javaScriptSnippet;
        }
    }

    /**
     * @return void
     */
    public static function addJavaScriptURL($javaScriptURL, $isAsync = false)
    {
        self::$javaScriptURLs[$javaScriptURL] = $isAsync;
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
     * @return void
     */
    public static function render()
    {
        $bodyContent = ob_get_clean();

        ob_start();

        ?>

        <!doctype html>
        <html lang="en">
            <head>
                <meta charset="UTF-8">
                <title><?php echo self::$titleHTML; ?></title>
                <meta name="description" content="<?php echo self::$descriptionHTML; ?>">
                <?php self::renderCSSLinks(); ?>
            </head>
            <body>
                <?php echo $bodyContent; $bodyContent = null;?>
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

        foreach (self::$javaScriptSnippets as $snippet)
        {
            include $snippet;
        }
    }

    /**
     * @return void
     */
    public static function reset()
    {
        if (self::$isActive)
        {
            restore_exception_handler();

            ob_end_clean();
        }

        self::$CSSURLs              = array();
        self::$descriptionHTML      = '';
        self::$exportedLists        = array();
        self::$exportedVariables    = array();
        self::$isActive             = false;
        self::$javaScriptSnippets   = array();
        self::$javaScriptURLs       = array();
        self::$titleHTML            = '';
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
}

CBHTMLOutput::reset();

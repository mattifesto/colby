<?php

define('MARKAROUND_STATE_BLOCK_QUOTE',               0);
define('MARKAROUND_STATE_DESCRIPTION_LIST',          1);
define('MARKAROUND_STATE_NONE',                      2);
define('MARKAROUND_STATE_ORDERED_LIST',              3);
define('MARKAROUND_STATE_PRE_FORMATTED',             4);
define('MARKAROUND_STATE_SIMPLE_PARAGRAPH',          5);
define('MARKAROUND_STATE_UNORDERED_LIST',            6);

define('MARKAROUND_LINE_TYPE_BLOCK_QUOTE',           0);
define('MARKAROUND_LINE_TYPE_DESCRIPTION_NAME',      1);
define('MARKAROUND_LINE_TYPE_DESCRIPTION_VALUE',     2);
define('MARKAROUND_LINE_TYPE_EMPTY',                 3);
define('MARKAROUND_LINE_TYPE_HEADING1',              4);
define('MARKAROUND_LINE_TYPE_HEADING2',              5);
define('MARKAROUND_LINE_TYPE_HEADING3',              6);
define('MARKAROUND_LINE_TYPE_ORDERED_LIST_ITEM',     7);
define('MARKAROUND_LINE_TYPE_PRE_FORMATTED',         8);
define('MARKAROUND_LINE_TYPE_TEXT_LEFT',             9);
define('MARKAROUND_LINE_TYPE_TEXT_INDENTED',        10);
define('MARKAROUND_LINE_TYPE_UNORDERED_LIST_ITEM',  11);

class ColbyMarkaroundParser
{
    private $currentMarkaroundLine;
    private $currentMarkaroundLineType;
    private $currentParagraphText;
    private $currentState;

    private $htmlArray;
    private $html;
    private $markaround;

    /**
     * The constructor is private because instances of this class should be
     * created using the static method constructor.
     *
     * @return ColbyMarkaroundParser
     */
    private function __construct()
    {
    }

    /**
     * Creates a ColbyMarkaroundParser to parse the provided markaround text.
     *
     * @param string $markaround
     *
     * @return ColbyMarkaroundParser
     */
    static function parserWithMarkaround($markaround)
    {
        $parser = new ColbyMarkaroundParser();
        $parser->markaround = $markaround;

        return $parser;
    }

    /**
     * @return string
     *  The html generated from the markaround text.
     */
    public function html()
    {
        if (!$this->html)
        {
            $this->parse();
        }

        return $this->html;
    }

    /**
     * @return string
     */
    private function currentLineContentText()
    {
        switch ($this->currentMarkaroundLineType)
        {
            case MARKAROUND_LINE_TYPE_BLOCK_QUOTE:

                return preg_replace(
                    '/^>\s*(.*)\s*$/',
                    '$1',
                    $this->currentMarkaroundLine
                );

            case MARKAROUND_LINE_TYPE_DESCRIPTION_NAME:

                return preg_replace(
                    '/^}\s*(.*)\s*$/',
                    '$1',
                    $this->currentMarkaroundLine
                );

            case MARKAROUND_LINE_TYPE_DESCRIPTION_VALUE:

                return preg_replace(
                    '/^]\s*(.*)\s*$/',
                    '$1',
                    $this->currentMarkaroundLine
                );

            case MARKAROUND_LINE_TYPE_ORDERED_LIST_ITEM:

                return preg_replace(
                    '/^[0-9]+\.\s*(.*)\s*$/',
                    '$1',
                    $this->currentMarkaroundLine
                );

            case MARKAROUND_LINE_TYPE_PRE_FORMATTED:

                /**
                 * The sequence of four backslashes below represents a single
                 * backslash.
                 *
                 * http://php.net/manual/en/regexp.reference.escape.php
                 */

                return preg_replace(
                    '/^\)\s*(\\\\)?(.*)\s*$/',
                    '$2',
                    $this->currentMarkaroundLine
                );

            case MARKAROUND_LINE_TYPE_TEXT_INDENTED:
            case MARKAROUND_LINE_TYPE_TEXT_LEFT:

                return preg_replace(
                    '/^(\\\\)?\s*(.*)\s*$/',
                    '$2',
                    $this->currentMarkaroundLine
                );

            case MARKAROUND_LINE_TYPE_UNORDERED_LIST_ITEM:

                return preg_replace(
                    '/^-\s*(.*)\s*$/',
                    '$1',
                    $this->currentMarkaroundLine
                );

            default:

                return '';
        }
    }

    /**
     * @return MARKAROUND_LINE_TYPE
     */
    private function currentMarkaroundLineType()
    {
        // Test in order of likelyhood where possible.

        if (preg_match('/^\s*$/', $this->currentMarkaroundLine))
        {
            return MARKAROUND_LINE_TYPE_EMPTY;
        }
        else if (preg_match('/^>/', $this->currentMarkaroundLine))
        {
            // Block quote lines don't have to have content.

            return MARKAROUND_LINE_TYPE_BLOCK_QUOTE;
        }
        else if (preg_match('/^}\s*./', $this->currentMarkaroundLine))
        {
            return MARKAROUND_LINE_TYPE_DESCRIPTION_NAME;
        }
        else if (preg_match('/^]\s*./', $this->currentMarkaroundLine))
        {
            return MARKAROUND_LINE_TYPE_DESCRIPTION_VALUE;
        }
        else if (preg_match('/^#[^#]\s*./', $this->currentMarkaroundLine))
        {
            return MARKAROUND_LINE_TYPE_HEADING1;
        }
        else if (preg_match('/^##[^#]\s*./', $this->currentMarkaroundLine))
        {
            return MARKAROUND_LINE_TYPE_HEADING2;
        }
        else if (preg_match('/^###[^#]\s*./', $this->currentMarkaroundLine))
        {
            return MARKAROUND_LINE_TYPE_HEADING3;
        }
        else if (preg_match('/^[0-9]+\.\s+./', $this->currentMarkaroundLine))
        {
            return MARKAROUND_LINE_TYPE_ORDERED_LIST_ITEM;
        }
        else if (preg_match('/^\)/', $this->currentMarkaroundLine))
        {
            // Pre-formatted lines don't have to have content.
            return MARKAROUND_LINE_TYPE_PRE_FORMATTED;
        }
        else if (preg_match('/^-\s*./', $this->currentMarkaroundLine))
        {
            return MARKAROUND_LINE_TYPE_UNORDERED_LIST_ITEM;
        }
        else if (preg_match('/^\s+./', $this->currentMarkaroundLine))
        {
            return MARKAROUND_LINE_TYPE_TEXT_INDENTED;
        }
        else
        {
            return MARKAROUND_LINE_TYPE_TEXT_LEFT;
        }
    }

    /**
     * @return void
     */
    private function flushCurrentParagraph(): void {
        if ($this->currentParagraphText)
        {
            $html = CBMarkaround::paragraphToHTML($this->currentParagraphText);
            $html = "<p>{$html}\n";

            $this->htmlArray[] = $html;

            $this->currentParagraphText = null;
        }
    }


    /**
     * @return void
     */
    private function parse(): void {
        $this->currentState     = MARKAROUND_STATE_NONE;
        $this->htmlArray        = array();
        $markaroundLines        = CBConvert::stringToLines($this->markaround);

        foreach ($markaroundLines as $markaroundLine)
        {
            $this->currentMarkaroundLine = $markaroundLine;

            $this->currentMarkaroundLineType =
            $this->currentMarkaroundLineType();

            $isLineProcessed = false;

            while (!$isLineProcessed)
            {
                switch ($this->currentState)
                {
                    case MARKAROUND_STATE_BLOCK_QUOTE:

                        $newState = $this->processLineForBlockQuoteState();
                        break;

                    case MARKAROUND_STATE_DESCRIPTION_LIST:

                        $newState = $this->processLineForDescriptionListState();
                        break;

                    case MARKAROUND_STATE_NONE:

                        $newState = $this->processLineForNoState();
                        break;

                    case MARKAROUND_STATE_ORDERED_LIST:

                        $newState = $this->processLineForOrderedListState();
                        break;

                    case MARKAROUND_STATE_PRE_FORMATTED:

                        $newState = $this->processLineForPreFormattedState();
                        break;

                    case MARKAROUND_STATE_SIMPLE_PARAGRAPH:

                        $newState = $this->processLineForSimpleParagraphState();
                        break;

                    case MARKAROUND_STATE_UNORDERED_LIST:

                        $newState = $this->processLineForUnorderedListState();
                        break;

                    default:

                        throw new RuntimeException('Unknown markaround state.');
                }

                if ($this->currentState == $newState) {
                    $isLineProcessed = true;
                } else {
                    /**
                     * All state transitions are either going to or from
                     * MARKAROUND_STATE_NONE. Anything else is an error.
                     */

                    if ($this->currentState == MARKAROUND_STATE_NONE)
                    {
                        $this->transitionFromNoStateTo($newState);
                    }
                    else if ($newState == MARKAROUND_STATE_NONE)
                    {
                        $this->transitionToNoStateFrom($this->currentState);
                    }
                    else
                    {
                        $message = (
                            "Unsupported state transition from " .
                            "{$this->currentState} to {$newState}"
                        );

                        throw new RuntimeException($message);
                    }
                }
            }
        }

        // The last line has been processed so transition back to 'no state' to
        // flush the current paragraph and end the current element if necessary.

        $this->transitionToNoStateFrom($this->currentState);

        $this->html = implode('', $this->htmlArray);
        $this->htmlArray = null;
    }
    /* parse() */


    /**
     * @return MARKAROUND_STATE
     */
    private function processLineForBlockQuoteState()
    {
        $newState = MARKAROUND_STATE_BLOCK_QUOTE;

        switch ($this->currentMarkaroundLineType)
        {
            case MARKAROUND_LINE_TYPE_BLOCK_QUOTE:

                $lineContentText = $this->currentLineContentText();

                if (!$lineContentText)
                {
                    // No line content means a paragraph may be complete.

                    $this->flushCurrentParagraph();
                }
                else
                {
                    // Line content should be added to the current paragraph.
                    // It may be the first content of the paragraph.

                    if ($this->currentParagraphText)
                    {
                        $this->currentParagraphText .= ' ';
                    }

                    $this->currentParagraphText .= $lineContentText;
                }

                break;

            default:

                $newState = MARKAROUND_STATE_NONE;

                break;
        }

        return $newState;
    }

    /**
     * @return MARKAROUND_STATE
     */
    private function processLineForDescriptionListState()
    {
        $newState = MARKAROUND_STATE_DESCRIPTION_LIST;

        switch ($this->currentMarkaroundLineType)
        {
            case MARKAROUND_LINE_TYPE_DESCRIPTION_NAME:

                $this->flushCurrentParagraph();

                $this->currentParagraphText = $this->currentLineContentText();
                $this->htmlArray[] = '<dt>';

                break;

            case MARKAROUND_LINE_TYPE_DESCRIPTION_VALUE:

                $this->flushCurrentParagraph();

                $this->currentParagraphText = $this->currentLineContentText();
                $this->htmlArray[] = '<dd>';

                break;

            case MARKAROUND_LINE_TYPE_EMPTY:

                $this->flushCurrentParagraph();

                break;

            case MARKAROUND_LINE_TYPE_TEXT_INDENTED:

                if ($this->currentParagraphText)
                {
                    $this->currentParagraphText .= ' ';
                }

                $this->currentParagraphText .= $this->currentLineContentText();

                break;

            case MARKAROUND_LINE_TYPE_TEXT_LEFT:

                if ($this->currentParagraphText)
                {
                    $this->currentParagraphText .= ' ';

                    $this->currentParagraphText .=
                    $this->currentLineContentText();
                }
                else
                {
                    $newState = MARKAROUND_STATE_NONE;
                }

                break;

            default:

                $newState = MARKAROUND_STATE_NONE;

                break;
        }

        return $newState;
    }

    /**
     * @return MARKAROUND_STATE
     */
    private function processLineForNoState()
    {
        $newState = MARKAROUND_STATE_NONE;

        switch ($this->currentMarkaroundLineType)
        {
            case MARKAROUND_LINE_TYPE_BLOCK_QUOTE:

                $newState = MARKAROUND_STATE_BLOCK_QUOTE;

                break;

            case MARKAROUND_LINE_TYPE_DESCRIPTION_NAME:
            case MARKAROUND_LINE_TYPE_DESCRIPTION_VALUE:

                $newState = MARKAROUND_STATE_DESCRIPTION_LIST;

                break;

            case MARKAROUND_LINE_TYPE_EMPTY:

                break;

            case MARKAROUND_LINE_TYPE_HEADING1:

                $lineContentText = preg_replace(
                    '/^#\s*(.*)\s*$/',
                    '$1',
                    $this->currentMarkaroundLine
                );

                $lineHTML = cbhtml($lineContentText);
                $this->htmlArray[] = "<h1>{$lineHTML}</h1>\n";

                break;

            case MARKAROUND_LINE_TYPE_HEADING2:

                $lineContentText = preg_replace(
                    '/^##\s*(.*)\s*$/',
                    '$1',
                    $this->currentMarkaroundLine
                );

                $lineHTML = cbhtml($lineContentText);
                $this->htmlArray[] = "<h2>{$lineHTML}</h2>\n";

                break;

            case MARKAROUND_LINE_TYPE_HEADING3:

                $lineContentText = preg_replace(
                    '/^###\s*(.*)\s*$/',
                    '$1',
                    $this->currentMarkaroundLine
                );

                $lineHTML = cbhtml($lineContentText);
                $this->htmlArray[] = "<h3>{$lineHTML}</h3>\n";

                break;

            case MARKAROUND_LINE_TYPE_ORDERED_LIST_ITEM:

                $newState = MARKAROUND_STATE_ORDERED_LIST;

                break;

            case MARKAROUND_LINE_TYPE_PRE_FORMATTED:

                $newState = MARKAROUND_STATE_PRE_FORMATTED;

                break;

            case MARKAROUND_LINE_TYPE_TEXT_LEFT:
            case MARKAROUND_LINE_TYPE_TEXT_INDENTED:

                $newState = MARKAROUND_STATE_SIMPLE_PARAGRAPH;

                break;

            case MARKAROUND_LINE_TYPE_UNORDERED_LIST_ITEM:

                $newState = MARKAROUND_STATE_UNORDERED_LIST;

                break;

            default:

                $lineHTML = cbhtml($this->currentMarkaroundLine);

                $this->htmlArray[] =
                "<p>{$this->currentMarkaroundLineType}: {$lineHTML}\n";

                break;

        }

        return $newState;
    }

    /**
     * @return MARKAROUND_STATE
     */
    private function processLineForOrderedListState()
    {
        $newState = MARKAROUND_STATE_ORDERED_LIST;

        switch ($this->currentMarkaroundLineType)
        {
            case MARKAROUND_LINE_TYPE_ORDERED_LIST_ITEM:

                $this->flushCurrentParagraph();

                $this->currentParagraphText = $this->currentLineContentText();
                $this->htmlArray[] = '<li>';

                break;

            case MARKAROUND_LINE_TYPE_EMPTY:

                $this->flushCurrentParagraph();

                break;

            case MARKAROUND_LINE_TYPE_TEXT_INDENTED:

                if ($this->currentParagraphText)
                {
                    $this->currentParagraphText .= ' ';
                }

                $this->currentParagraphText .= $this->currentLineContentText();

                break;

            case MARKAROUND_LINE_TYPE_TEXT_LEFT:

                if ($this->currentParagraphText)
                {
                    $this->currentParagraphText .= ' ';

                    $this->currentParagraphText .=
                    $this->currentLineContentText();
                }
                else
                {
                    $newState = MARKAROUND_STATE_NONE;
                }

                break;

            default:

                $newState = MARKAROUND_STATE_NONE;

                break;
        }

        return $newState;
    }

    /**
     * @return MARKAROUND_STATE
     */
    private function processLineForPreFormattedState()
    {
        $newState = MARKAROUND_STATE_PRE_FORMATTED;

        switch ($this->currentMarkaroundLineType)
        {
            case MARKAROUND_LINE_TYPE_PRE_FORMATTED:

                $lineHTML = cbhtml($this->currentLineContentText());
                $this->htmlArray[] = "{$lineHTML}\n";

                break;

            default:

                $newState = MARKAROUND_STATE_NONE;

                break;
        }

        return $newState;
    }

    /**
     * @return MARKAROUND_STATE
     */
    private function processLineForSimpleParagraphState()
    {
        $newState = MARKAROUND_STATE_SIMPLE_PARAGRAPH;

        switch ($this->currentMarkaroundLineType)
        {
            case MARKAROUND_LINE_TYPE_TEXT_LEFT:
            case MARKAROUND_LINE_TYPE_TEXT_INDENTED:

                if ($this->currentParagraphText)
                {
                    $this->currentParagraphText .= ' ';
                }

                $this->currentParagraphText .= $this->currentLineContentText();

                break;

            default:

                $newState = MARKAROUND_STATE_NONE;

                break;
        }

        return $newState;
    }

    /**
     * @return MARKAROUND_STATE
     */
    private function processLineForUnorderedListState()
    {
        $newState = MARKAROUND_STATE_UNORDERED_LIST;

        switch ($this->currentMarkaroundLineType)
        {
            case MARKAROUND_LINE_TYPE_UNORDERED_LIST_ITEM:

                $this->flushCurrentParagraph();

                $this->currentParagraphText = $this->currentLineContentText();
                $this->htmlArray[] = '<li>';

                break;

            case MARKAROUND_LINE_TYPE_EMPTY:

                $this->flushCurrentParagraph();

                break;

            case MARKAROUND_LINE_TYPE_TEXT_INDENTED:

                if ($this->currentParagraphText)
                {
                    $this->currentParagraphText .= ' ';
                }

                $this->currentParagraphText .= $this->currentLineContentText();

                break;

            case MARKAROUND_LINE_TYPE_TEXT_LEFT:

                if ($this->currentParagraphText)
                {
                    $this->currentParagraphText .= ' ';

                    $this->currentParagraphText .=
                    $this->currentLineContentText();
                }
                else
                {
                    $newState = MARKAROUND_STATE_NONE;
                }

                break;

            default:

                $newState = MARKAROUND_STATE_NONE;

                break;
        }

        return $newState;
    }

    /**
     * @return void
     */
    private function transitionFromNoStateTo($newState)
    {
        switch($newState)
        {
            case MARKAROUND_STATE_BLOCK_QUOTE:

                $this->htmlArray[] = "<blockquote>\n";
                break;

            case MARKAROUND_STATE_DESCRIPTION_LIST:

                $this->htmlArray[] = "<dl>\n";
                break;

            case MARKAROUND_STATE_ORDERED_LIST:

                $this->htmlArray[] = "<ol>\n";
                break;

            case MARKAROUND_STATE_PRE_FORMATTED:

                $this->htmlArray[] = "<pre>\n";
                break;

            case MARKAROUND_STATE_UNORDERED_LIST:

                $this->htmlArray[] = "<ul>\n";
                break;
        }

        $this->currentState = $newState;
    }

    /**
     * @return void
     */
    private function transitionToNoStateFrom($previousState)
    {
        $this->flushCurrentParagraph();

        switch($previousState)
        {
            case MARKAROUND_STATE_BLOCK_QUOTE:

                $this->htmlArray[] = "</blockquote>\n";
                break;

            case MARKAROUND_STATE_DESCRIPTION_LIST:

                $this->htmlArray[] = "</dl>\n";
                break;

            case MARKAROUND_STATE_ORDERED_LIST:

                $this->htmlArray[] = "</ol>\n";
                break;

            case MARKAROUND_STATE_PRE_FORMATTED:

                $this->htmlArray[] = "</pre>\n";
                break;

            case MARKAROUND_STATE_UNORDERED_LIST:

                $this->htmlArray[] = "</ul>\n";
                break;
        }

        $this->currentState = MARKAROUND_STATE_NONE;
    }
}

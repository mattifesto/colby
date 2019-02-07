<?php

final class CBMessageMarkup {

    const encodedBackslash = '836f784bf25aa0e5663779c36899c61efbaa114e';
    const encodedOpenBracket = 'f5a6328bda8575b25bbc5f0ece0181df57e54ed1';
    const encodedCloseBracket = 'edc679d4ac06a45884a23160030c4cb2d4b2ebf1';
    const encodedHyphen = '4605702366f1f3d132e1a76a25165e2c0b6b352c';

    /**
     * @return string
     */
    static function CBHTMLOutput_JavaScriptURLs() {
        return [Colby::flexpath(__CLASS__, 'v434.js', cbsysurl())];
    }

    /**
     * @deprecated use CBMessageMarkup::messageToHTML()
     */
    static function convert(string $markup): string {
        return CBMessageMarkup::messageToHTML($markup);
    }

    /**
     * @param [object] $stack
     *
     * @return object
     *
     *      {
     *          children: [mixed],
     *          classNamesAsHTML: string
     *          defaultChildTagName: string
     *          isPreformatted: bool
     *          tagName: string
     *      }
     */
    static function createElement(array &$stack) {
        $element = (object)[
            'children' => [],
            'classNamesAsHTML' => '',
            'defaultChildTagName' => '',
            'isPreformatted' => false,
            'tagName' => '',
        ];

        if (!empty($stack)) {
            $parentElement = end($stack);
            $parentElement->children[] = $element;
        }

        $stack[] = $element;

        return $element;
    }

    /**
     * Once a paragraph has been converted this will decode encoded markup
     * characters to their unescaped form.
     *
     * @param string $markup
     *
     * @return string
     */
    static function decodeEncodedCharacters(string $markup): string {
        $encodedBackslash = CBMessageMarkup::encodedBackslash;
        $encodedOpenBracket = CBMessageMarkup::encodedOpenBracket;
        $encodedCloseBracket = CBMessageMarkup::encodedCloseBracket;
        $encodedHyphen = CBMessageMarkup::encodedHyphen;

        $patterns = [
            "/{$encodedBackslash}/",
            "/{$encodedOpenBracket}/",
            "/{$encodedCloseBracket}/",
            "/{$encodedHyphen}/",
        ];
        $replacements = [
            '\\',
            '(',
            ')',
            '-',
        ];

        return preg_replace($patterns, $replacements, $markup);
    }


    /**
     * @param [object] $stack
     *
     * @return object|null
     */
    static function elementFinish(array &$stack) {
        $element = array_pop($stack);
        $html = '';

        foreach ($element->children as $child) {
            if (isset($child->html)) {
                $html .= $child->html;
            } else {
                if (!is_string($child)) {
                    throw new RuntimeException('This element child should be a string.');
                }

                $paragraphAsHTML = CBMessageMarkup::paragraphToHTML($child);

                switch ($element->defaultChildTagName) {
                    case '':
                        $html .= $paragraphAsHTML;
                        break;
                    case 'p':
                        $html .= "<p>{$paragraphAsHTML}";
                        break;
                    default:
                        $html .= "<{$element->defaultChildTagName}>\n" .
                                 "<p>{$paragraphAsHTML}" .
                                 "</{$element->defaultChildTagName}>\n";
                        break;
                }
            }
        }

        if (empty($stack)) {
            /* root element */
            $element->html = $html;
        } else {
            if ($element->classNamesAsHTML) {
                $classAttribute = " class=\"{$element->classNamesAsHTML}\"";
            } else {
                $classAttribute = '';
            }

            switch ($element->tagName) {
                case 'p':
                    $element->html = "<{$element->tagName}{$classAttribute}>{$html}";
                    break;
                default:
                    $element->html = "<{$element->tagName}{$classAttribute}>\n{$html}</{$element->tagName}>\n";
                    break;
            }
        }

        return end($stack);
    }

    /**
     * Encodes escaped markup characters in preparation for the paragraph to be
     * converted.
     *
     * @param string $markup
     *
     * @return string
     */
    static function encodeEscapedCharacters(string $markup): string {
        $escapedBackslashExpression = '/\\\\\\\\/' ;    /* double backslash */
        $escapedOpenBracketExpression = '/\\\\\\(/';    /* backslash, open bracket */
        $escapedCloseBracketExpression = '/\\\\\\)/';   /* backslash, close bracket */
        $escapedCommandExpression = '/\\\\-/';          /* backslash, hyphen */

        $patterns = [
            $escapedBackslashExpression,
            $escapedOpenBracketExpression,
            $escapedCloseBracketExpression,
            $escapedCommandExpression,
        ];
        $replacements = [
            CBMessageMarkup::encodedBackslash,
            CBMessageMarkup::encodedOpenBracket,
            CBMessageMarkup::encodedCloseBracket,
            CBMessageMarkup::encodedHyphen,
        ];

        return preg_replace($patterns, $replacements, $markup);
    }

    /**
     * @param string $matches[0]
     *
     *      "( Extra! ( strong))"
     *      "(Wikipedia ( a   https://www.wikipedia.org ))"
     *      "((  br  ))"
     *
     * @param string $matches[1]
     *
     *      " Extra! "
     *      "Wikipedia "
     *      ""
     *
     * @param string? $matches[2]
     *
     *      " strong"
     *      " a   https://www.wikipedia.org "
     *      "  br  "
     *
     * @return string
     */
    static function inlineElementToHTML(array $matches): string {
        $inlineContent = trim($matches[1]);
        $inlineTagData = preg_split('/\s+/', $matches[2], 2, PREG_SPLIT_NO_EMPTY);
        $inlineTagName = $inlineTagData[0];
        $inlineTagAttributeValue = isset($inlineTagData[1]) ? trim($inlineTagData[1]) : '';

        switch ($inlineTagName) {
            case 'br':
            case 'wbr':
                return "<{$inlineTagName}>";
                break;
            case 'a':
                return "<a href=\"{$inlineTagAttributeValue}\">{$inlineContent}</a>";
                break;
            case 'abbr':
                return "<abbr title=\"{$inlineTagAttributeValue}\">{$inlineContent}</abbr>";
                break;
            case 'bdo':
                return "<bdo dir=\"{$inlineTagAttributeValue}\">{$inlineContent}</bdo>";
                break;
            case 'data':
                return "<data value=\"{$inlineTagAttributeValue}\">{$inlineContent}</data>";
                break;
            case 'time':
                return "<time datetime=\"{$inlineTagAttributeValue}\">{$inlineContent}</time>";
                break;
            case 'b':
            case 'bdi':
            case 'cite':
            case 'code':
            case 'dfn':
            case 'em':
            case 'i':
            case 'kbd':
            case 'mark':
            case 'q':
            case 'rp':
            case 'rt':
            case 'rtc':
            case 'ruby':
            case 's':
            case 'samp':
            case 'small':
            case 'span':
            case 'strong':
            case 'sub':
            case 'sup':
            case 'u':
            case 'var':
                return "<{$inlineTagName}>{$inlineContent}</{$inlineTagName}>";
                break;
            default:
                return "<span class=\"{$inlineTagName}\">{$inlineContent}</span>";
                break;
        }
    }

    /**
     * @param string $matches[0]
     *
     *      "( Extra! ( strong))"
     *      "(Wikipedia ( a   https://www.wikipedia.org ))"
     *      "((  br  ))"
     *
     * @param string $matches[1]
     *
     *      " Extra! "
     *      "Wikipedia "
     *      ""
     *
     * @param string? $matches[2]
     *
     *      " strong"
     *      " a   https://www.wikipedia.org "
     *      "  br  "
     *
     * @return string
     */
    static function inlineElementToText(array $matches): string {
        $inlineContent = trim($matches[1]);
        $inlineTagData = preg_split('/\s+/', $matches[2], 2, PREG_SPLIT_NO_EMPTY);
        $inlineTagName = $inlineTagData[0];
        $inlineTagAttributeValue = isset($inlineTagData[1]) ? trim($inlineTagData[1]) : '';

        switch ($inlineTagName) {
            case 'br':
            case 'wbr':
                return '';
                break;
            default:
                return $inlineContent;
                break;
        }
    }

    /**
     * @param string $line
     *
     * @return object|null
     *
     *      {
     *          classNames: [string]
     *          tagName: string
     *      }
     */
    private static function lineToCommand(string $line): ?stdClass {
        $properties = (object)[];

        if (1 !== preg_match('/^\s*---(\s.*)?$/', $line, $matches)) {
            return null;
        }

        $classNames = isset($matches[1]) ? trim($matches[1]) : '';

        if ($classNames === '') {
            $classNames = [];
            $tagName = "";
        } else {
            $classNames = preg_split('/\s+/', $classNames);

            /**
             * The first word will be used as the tag name if it is a valid tag
             * name. otherwise the tag name will be "div".
             */

            if (CBMessageMarkup::tagNameIsAllowedBlockElement($classNames[0])) {
                $tagName = array_shift($classNames);
            } else {
                $tagName = 'div';
            }
        }

        return (object)[
            'classNames' => $classNames,
            'tagName' => $tagName,
        ];
    }

    /**
     * @deprecated use messageToHTML()
     */
    static function markupToHTML(string $message): string {
        return CBMessageMarkup::messageToHTML($message);
    }

    /**
     * @deprecated use messageToText()
     */
    static function markupToText(string $message): string {
        return CBMessageMarkup::messageToText($message);
    }

    /**
     * @param string $markup
     *
     * @return string
     */
    static function messageToHTML(string $markup): string {
        $markup = CBMessageMarkup::encodeEscapedCharacters($markup);
        $content = null;
        $lines = preg_split("/\r\n|\n|\r/", $markup);
        $stack = [];
        $rootElement = CBMessageMarkup::createElement($stack);
        $rootElement->defaultChildTagName = 'p';
        $rootElement->tagName = 'root';
        $currentElement = $rootElement;

        for ($index = 0; $index < count($lines); $index++) {
            $line = $lines[$index];
            $command = CBMessageMarkup::lineToCommand($line);

            // Command lines

            if ($command !== null) {
                $parentAllows = CBMessageMarkup::tagNameAllowsBlockChildren($currentElement->tagName);

                if ($parentAllows && $command->tagName !== '') {
                    if ($content !== null) {
                        $currentElement->children[] = $content;
                        $content = null;
                    }

                    $currentElement = CBMessageMarkup::createElement($stack);
                    $currentElement->classNamesAsHTML = cbhtml(implode(' ', $command->classNames));
                    $currentElement->tagName = $command->tagName;

                    switch ($command->tagName) {
                        case 'dl':
                            $currentElement->defaultChildTagName = 'dd';
                            break;
                        case 'h1':
                        case 'h2':
                        case 'h3':
                        case 'h4':
                        case 'h5':
                        case 'h6':
                        case 'p':
                            break;
                        case 'ol':
                        case 'ul':
                            $currentElement->defaultChildTagName = 'li';
                            break;
                        case 'pre':
                            $currentElement->isPreformatted = true;
                            break;
                        default:
                            $currentElement->defaultChildTagName = 'p';
                            break;
                    }
                }

                if ($command->tagName === '' && $currentElement !== $rootElement) {
                    if ($content !== null) {
                        $currentElement->children[] = $content;
                        $content = null;
                    }

                    $currentElement = CBMessageMarkup::elementFinish($stack);
                }

                continue;
            }

            // Content lines

            if ($currentElement->isPreformatted || trim($line) !== '') {
                if ($content === null) {
                    $content = '';
                }

                $content .= "{$line}\n";
            } else {
                if ($content !== null) {
                    $currentElement->children[] = $content;
                    $content = null;
                }
            }
        }

        // After processing every line

        if ($content !== null) {
            $currentElement->children[] = $content;
            $content = null;
        }

        while (!empty($stack)) {
            $currentElement = CBMessageMarkup::elementFinish($stack);
        }

        $html = CBMessageMarkup::decodeEncodedCharacters($rootElement->html);

        return $html;
    }

    /**
     * Converts a message to plain text. Useful for creating search text or
     * plain text summaries.
     *
     * @param string $markup
     *
     * @return string
     */
    static function messageToText(string $markup): string {
        $markup = CBMessageMarkup::encodeEscapedCharacters($markup);
        $paragraphs = [];
        $paragraph = null;
        $lines = preg_split("/\r\n|\n|\r/", $markup);

        for ($index = 0; $index < count($lines); $index++) {
            $line = $lines[$index];
            $command = CBMessageMarkup::lineToCommand($line);

            /**
             * TODO: Add support for recognizing preformatted elements.
             */

            if ($command !== null || trim($line) === '') {
                if ($paragraph !== null) {
                    $paragraph = CBMessageMarkup::paragraphToText($paragraph);

                    $paragraphs[] = $paragraph;

                    $paragraph = null;
                }
            } else {
                if ($paragraph === null) {
                    $paragraph = '';
                }

                $paragraph .= "{$line}\n";
            }
        }

        // After processing every line

        if ($paragraph !== null) {
            $paragraph = CBMessageMarkup::paragraphToText($paragraph);
            $paragraphs[] = $paragraph;
            $paragraph = null;
        }

        $text = implode("\n", $paragraphs);
        $text = CBMessageMarkup::decodeEncodedCharacters($text);

        return $text;
    }

    /**
     * @param string $markup
     *
     * @return string
     */
    static function paragraphToHTML(string $markup): string {
        $content = cbhtml($markup);

        $openBracket = '\\(';
        $closeBracket = '\\)';
        $notBracket = '[^\\(\\)]';

        // No 'g' modifier in php because preg_replace always does all.
        $inlineElementExpression = "/{$openBracket}({$notBracket}*){$openBracket}({$notBracket}+){$closeBracket}\s*{$closeBracket}/";

        do {
            $content = preg_replace_callback($inlineElementExpression, 'CBMessageMarkup::inlineElementToHTML', $content, -1, $count);
        } while ($count > 0);

        return $content;
    }

    /**
     * @param string $markup
     *
     * @return string
     */
    static function paragraphToText(string $markup) {
        $content = $markup;

        $openBracket = '\\(';
        $closeBracket = '\\)';
        $notBracket = '[^\\(\\)]';

        // No 'g' modifier in php because preg_replace always does all.
        $inlineElementExpression = "/{$openBracket}({$notBracket}*){$openBracket}({$notBracket}+){$closeBracket}\s*{$closeBracket}/";

        do {
            $content = preg_replace_callback($inlineElementExpression, 'CBMessageMarkup::inlineElementToText', $content, -1, $count);
        } while ($count > 0);

        return wordwrap(
            trim(
                preg_replace('/\s+/', ' ', $content)
            ),
            80,
            "\n", /* line break character */
            true /* cut long words */
        );
    }

    /**
     * @deprecated use stringToMessage()
     */
    static function stringToMarkup(string $value): string {
        return CBMessageMarkup::stringToMessage($value);
    }

    /**
     * This function converts a string to a message representing that string as
     * plain text. This function is the `htmlspecialchars` of message markup.
     *
     * Conversions:
     *
     *     single backslash -> double backslash
     *     hyphen -> backslash hyphen
     *     open bracket -> backslash, open bracket
     *     close bracket -> backslash, close bracket
     *
     * @NOTE
     *
     *      A single backslash in a regular expression or a preg_replace
     *      replacement is represented by four backslashes.
     *
     * @param string $value
     *
     * @return string
     */
    static function stringToMessage(string $value): string {
        $patterns = [
            '/\\\\/',   /* single backslack */
            '/-/',      /* hyphen */
            '/\(/',     /* open bracket */
            '/\)/',     /* close bracket */
        ];
        $replacements = [
            '\\\\\\\\', /* double backslash */
            '\\\\-',    /* backslash hyphen */
            '\\\\(',    /* backslash open bracket */
            '\\\\)',    /* backslash close bracket */
        ];

        return preg_replace($patterns, $replacements, $value);
    }

    /**
     * @param string $tagName
     *
     * @return bool
     */
    static function tagNameAllowsBlockChildren(string $tagName): bool {
        return in_array($tagName, [
            'blockquote',
            'dd',
            'div',
            'dl',
            'dt',
            'li',
            'ol',
            'root', // custom
            'ul',
        ]);
    }

    /**
     * @param string $tagName
     *
     * @return bool
     */
    static function tagNameIsAllowedBlockElement(string $tagName): bool {
        return in_array($tagName, [
            'blockquote',
            'dd',
            'div',
            'dl',
            'dt',
            'h1',
            'h2',
            'h3',
            'h4',
            'h5',
            'h6',
            'hr',
            'li',
            'p',
            'pre',
            'ol',
            'ul',
        ]);
    }
}

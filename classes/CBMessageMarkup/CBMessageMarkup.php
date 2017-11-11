<?php

final class CBMessageMarkup {

    /**
     * @return string
     */
    static function CBHTMLOutput_JavaScriptURLs() {
        return [Colby::flexpath(__CLASS__, 'js', cbsysurl())];
    }

    /**
     * @param string $message
     *
     * @return string
     */
    static function convert(string $message): string {
        $content = null;
        $lines = preg_split("/\r\n|\n|\r/", $message);
        $stack = [];
        $rootElement = CBMessageMarkup::createElement($stack);
        $rootElement->tagName = 'root';
        $currentElement = $rootElement;

        for ($index = 0; $index < count($lines); $index++) {
            $line = $lines[$index];
            $command = CBMessageMarkup::lineToCommand($line);

            // Command lines

            if ($command !== null) {
                $parentAllows = CBMessageMarkup::tagNameAllowsBlockChildren($currentElement->tagName);
                $childAllowed = CBMessageMarkup::tagNameIsAllowedBlockElement($command->tagName);

                if ($parentAllows && $childAllowed) {
                    if ($content !== null) {
                        $currentElement->children[] = $content;
                        $content = null;
                    }

                    $currentElement = CBMessageMarkup::createElement($stack);
                    $currentElement->classNamesAsHTML = cbhtml(implode(' ', $command->classNames));
                    $currentElement->tagName = $command->tagName;

                    switch ($command->tagName) {
                        case "pre":
                            $currentElement->isPreformatted = true;
                            break;
                        case "ol":
                        case "ul":
                            $currentElement->defaultChildTagName = "li";
                            break;
                        case "dl":
                            $currentElement->defaultChildTagName = "dd";
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

        return $rootElement->html;
    }

    /**
     * @param string $markup
     *
     * @return string
     */
    static function convertInline(string $markup): string {
        $content = cbhtml($markup);

        $escapedOpenBraceExpression = '/\\\\\\{/';
        $escapedCloseBraceExpression = '/\\\\\\}/';
        $escapedCommandExpression = '/^\\\\---/';
        $escapedOpenBraceReplacement = 'f5a6328bda8575b25bbc5f0ece0181df57e54ed1';
        $escapedCloseBraceReplacement = 'edc679d4ac06a45884a23160030c4cb2d4b2ebf1';
        $escapedCommandReplacement = '4605702366f1f3d132e1a76a25165e2c0b6b352c';

        $patterns = [
            $escapedOpenBraceExpression,
            $escapedCloseBraceExpression,
            $escapedCommandExpression,
        ];
        $replacements = [
            $escapedOpenBraceReplacement,
            $escapedCloseBraceReplacement,
            $escapedCommandReplacement,
        ];

        $content = preg_replace($patterns, $replacements, $content);

        // No 'g' modifier in php because preg_replace always does all.
        $inlineElementExpression = '/\\{([a-z]+):(?:\s+([^\\{\\}]+))?\\}/';

        do {
            $content = preg_replace_callback($inlineElementExpression, 'CBMessageMarkup::replace', $content, -1, $count);
        } while ($count > 0);

        $patterns = [
            "/{$escapedOpenBraceReplacement}/",
            "/{$escapedCloseBraceReplacement}/",
            "/{$escapedCommandReplacement}/",
        ];
        $replacements = [
            '{',
            '}',
            '---',
        ];

        $content = preg_replace($patterns, $replacements, $content);
        return $content;
    }

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

                $childHTML = CBMessageMarkup::convertInline($child);

                if ($element->isPreformatted) {
                    $html .= $childHTML;
                } else if ($element->defaultChildTagName) {
                    $html .= "<{$element->defaultChildTagName}>\n" .
                             "<p>{$childHTML}" .
                             "</{$element->defaultChildTagName}>\n";
                } else {
                    $html .= "<p>{$childHTML}";
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

            $element->html = "<{$element->tagName}{$classAttribute}>\n{$html}</{$element->tagName}>\n";
        }

        return end($stack);
    }

    /**
     * @param string $line
     *
     * @return object|false
     */
    private static function lineToCommand(string $line): ?stdClass {
        $properties = (object)[];

        if (1 !== preg_match('/^\s*---(\s.*)?$/', $line, $matches)) {
            return null;
        }

        if (isset($matches[1]) && ($classNames = trim($matches[1])) !== '') {
            $classNames = preg_split('/\s+/', $classNames);
            $tagName = array_shift($classNames);
        } else {
            $classNames = [];
            $tagName = "";
        }

        $val = (object)[
            'classNames' => $classNames,
            'tagName' => $tagName,
        ];

        return $val;
    }

    /**
     * @return string
     */
    static function replace(array $matches) {
        $inlineTagName = $matches[1];
        $inlineContent = $matches[2] ?? '';

        switch ($inlineTagName) {
            case 'br':
            case 'wbr':
                return "<{$inlineTagName}>";
                break;
            case 'a':
                if (1 === preg_match('/(.+)\shref:\s+(.+)/s', $inlineContent, $submatches)) {
                    $text = trim($submatches[1]);
                    $href = trim($submatches[2]);

                    return "<a href=\"{$href}\">{$text}</a>";
                }

                break;
            case 'b':
            case 'cite':
            case 'code':
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
            case 'time':
            case 'u':
            case 'var':
                return "<{$inlineTagName}>{$inlineContent}</{$inlineTagName}>";
                break;
        }

        return "<span class=\"{$inlineTagName}\">{$inlineContent}</span>";
    }

    /**
     * @param string $tagName
     *
     * @return bool
     */
    static function tagNameAllowsBlockChildren(string $tagName): bool {
        return in_array($tagName, [
            "blockquote",
            "dd",
            "div",
            "dl",
            "dt",
            "li",
            "ol",
            "root", // custom
            "ul",
        ]);
    }

    /**
     * @param string $tagName
     *
     * @return bool
     */
    static function tagNameIsAllowedBlockElement(string $tagName): bool {
        return in_array($tagName, [
            "blockquote",
            "dd",
            "div",
            "dl",
            "dt",
            "h1",
            "h2",
            "h3",
            "h4",
            "h5",
            "h6",
            "li",
            "pre",
            "ol",
            "ul",
        ]);
    }
}

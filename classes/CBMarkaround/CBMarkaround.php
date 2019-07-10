<?php

final class CBMarkaround {

    /**
     * @return string
     */
    private static function expressionForSpan(
        $openExpression,
        $closeExpression
    ): string {
        return "/

            {$openExpression}
            (
                (?=\S)      # content must start with a non-whitspace character
                .+?         # content always has at least one character
                (?<=\S)     # content must end with a non-whitespace character
            )
            {$closeExpression}

            /x";
    }


    /**
     * @param string $markaround
     *
     * @return string
     */
    static function markaroundToHTML($markaround) {
        $parser = ColbyMarkaroundParser::parserWithMarkaround($markaround);
        return $parser->html();
    }


    /**
     * TODO: 2016_01_07 This function still needs to be properly implemented.
     *
     * @param string $markaround
     *
     * @return string
     */
    static function markaroundToText($markaround) {
        return $markaround;
    }


    /**
     * @note functional programming
     *
     * Applies inline formatting rules to a paragraph of text.
     *
     * The reason the term "paragraph" is used is that inline formatting spans
     * must exist within a "paragraph" of text. For instance, you cannot start
     * bold text in one paragraph and then end it in the next. Whether or not
     * the caller thinks of the string argument as a "paragraph", this function
     * does think of it that way.
     *
     * @return {string}
     */
    private static function paragraphTo($paragraph, $args = []) {
        $format = 'HTML';
        extract($args, EXTR_IF_EXISTS);

        $escapes = array(
            '\\\\\\\\'  => '\\',    //  \\  -->  \
            '\\\\\/'    => '/',     //  \/  -->  /
            '\\\\\*'    => '*',     //  \*  -->  *
            '\\\\\{'    => '{',     //  \{  -->  {
            '\\\\\}'    => '}',     //  \}  -->  }
            '\\\\_'     => '_',     //  \_  -->  _
            '\\\\`'     => '`',     //  \`  -->  `
            '\\\\^'     => '^');    //  \^  -->  ^

        $paragraph = cbhtml($paragraph);

        foreach ($escapes as $pattern => $replacement) {
            $hash       = sha1($pattern);
            $paragraph  = preg_replace("/{$pattern}/", $hash, $paragraph);
        }

        switch ($format) {
            case 'HTML':
                $patterns[]     = CBMarkaround::expressionForSpan('\*', '\*');
                $replacements[] = '<b>$1</b>';
                $patterns[]     = CBMarkaround::expressionForSpan('_', '_');
                $replacements[] = '<i>$1</i>';
                $patterns[]     = CBMarkaround::expressionForSpan('{', '}');
                $replacements[] = '<cite>$1</cite>';
                $patterns[]     = CBMarkaround::expressionForSpan('`', '`');
                $replacements[] = '<code>$1</code>';
                $patterns[]     = CBMarkaround::expressionForSpan('\^', '\^');
                $replacements[] = '<span class="special">$1</span>';
                $patterns[]     = '/ (?<=^|\s) \/ (?=\s|$) /x';
                $replacements[] = '<br>';
                break;

            case 'text':
                $patterns[]     = CBMarkaround::expressionForSpan('\*', '\*');
                $replacements[] = '$1';
                $patterns[]     = CBMarkaround::expressionForSpan('_', '_');
                $replacements[] = '$1';
                $patterns[]     = CBMarkaround::expressionForSpan('{', '}');
                $replacements[] = '$1';
                $patterns[]     = CBMarkaround::expressionForSpan('`', '`');
                $replacements[] = '$1';
                $patterns[]     = CBMarkaround::expressionForSpan('\^', '\^');
                $replacements[] = '$1';
                $patterns[]     = '/ (?<=^|\s) \/ (?=\s|$) /x';
                $replacements[] = '';
                break;

            default:
                throw RuntimeException('Unrecognized format.');
                break;
        }

        $paragraph = preg_replace($patterns, $replacements, $paragraph);

        foreach ($escapes as $pattern => $replacement) {
            $hash       = sha1($pattern);
            $paragraph  = preg_replace("/{$hash}/", $replacement, $paragraph);
        }

        return $paragraph;
    }


    /**
     * @return {string}
     */
    static function paragraphToHTML($paragraph) {
        return CBMarkaround::paragraphTo($paragraph, ['format' => 'HTML']);
    }


    /**
     * @return {string}
     */
    static function paragraphToText($paragraph) {
        return CBMarkaround::paragraphTo($paragraph, ['format' => 'text']);
    }


    /**
     * @param string format
     *
     *  This specifies what kind of formatting the text uses. The only supported
     *  value currently is the default value `inline` which specifies to only
     *  format the inline markaround and not markaround for block level element,
     *  such as lists or block quotes.
     *
     * @return string
     */
    static function textToHTML($args) {
        $format = 'inline'; $text = '';
        extract($args);

        $lines = CBConvert::stringToLines((string)$text);
        $paragraphs = ColbyConvert::linesToParagraphs($lines);

        $paragraphs = array_map(
            'CBMarkaround::paragraphToHTML',
            $paragraphs
        );

        $paragraphs = array_map(
            function($p) {
                return "<p>{$p}";
            },
            $paragraphs
        );

        return implode("\n\n", $paragraphs);
    }
    /* textToHTML() */
}

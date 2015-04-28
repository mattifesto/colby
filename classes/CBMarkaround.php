<?php

final class CBMarkaround {

    /**
    @return string
    */
    private static function expressionForSpan($openExpression, $closeExpression) {
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
     * @return string
     */
    public static function paragraphToHTML($paragraph) {
        $escapes = array(
            '\\\\\\\\'  => '\\',    //  \\  -->  \
            '\\\\\/'    => '/',     //  \/  -->  /
            '\\\\\*'    => '*',     //  \*  -->  *
            '\\\\\{'    => '{',     //  \{  -->  {
            '\\\\\}'    => '}',     //  \}  -->  }
            '\\\\_'     => '_',     //  \_  -->  _
            '\\\\`'     => '`');    //  \`  -->  `

        $paragraph = ColbyConvert::textToHTML($paragraph);

        foreach ($escapes as $pattern => $replacement) {
            $hash       = sha1($pattern);
            $paragraph  = preg_replace("/{$pattern}/", $hash, $paragraph);
        }

        $patterns[]     = self::expressionForSpan('\*', '\*');
        $replacements[] = '<b>$1</b>';
        $patterns[]     = self::expressionForSpan('_', '_');
        $replacements[] = '<i>$1</i>';
        $patterns[]     = self::expressionForSpan('{', '}');
        $replacements[] = '<cite>$1</cite>';
        $patterns[]     = self::expressionForSpan('`', '`');
        $replacements[] = '<code>$1</code>';
        $patterns[]     = '/ (?<=^|\s) \/ (?=\s|$) /x';
        $replacements[] = '<br>';

        $paragraph = preg_replace($patterns, $replacements, $paragraph);

        foreach ($escapes as $pattern => $replacement) {
            $hash       = sha1($pattern);
            $paragraph  = preg_replace("/{$hash}/", $replacement, $paragraph);
        }

        return $paragraph;
    }

    /**
    @param {string} format
        This specifies what kind of formatting the text uses. The only supported value currently is the default value `inline` which specifies to only format the inline markaround and not markaround for block level element, such as lists or block quotes.

    @return string
    */
    public static function textToHTML($args) {
        $format = 'inline'; $text = '';
        extract($args);

        $lines          = ColbyConvert::textToLines((string)$text);
        $paragraphs     = ColbyConvert::linesToParagraphs($lines);
        $paragraphs     = array_map('CBMarkaround::paragraphToHTML', $paragraphs);
        $paragraphs     = array_map(function($p) { return "<p>{$p}"; }, $paragraphs);

        return implode("\n\n", $paragraphs);
    }
}

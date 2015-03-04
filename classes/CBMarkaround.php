<?php

class CBMarkaround {

    /**
     * #note functional programming
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

        //

        foreach ($escapes as $pattern => $replacement) {
            $hash       = sha1($pattern);
            $paragraph  = preg_replace("/{$hash}/", $replacement, $paragraph);
        }

        return $paragraph;
    }
}

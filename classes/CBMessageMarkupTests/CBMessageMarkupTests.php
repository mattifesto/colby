<?php

final class CBMessageMarkupTests {

    /**
     * @return [string]
     */
    static function CBHTMLOutput_JavaScriptURLs() {
        return [Colby::flexpath(__CLASS__, 'js', cbsysurl())];
    }

    /**
     * @return [[string, string]]
     */
    static function CBHTMLOutput_JavaScriptVariables() {
        return [
            ['CBMessageMarkupTests_html1', CBMessageMarkupTests::html1()],
            ['CBMessageMarkupTests_markup1', CBMessageMarkupTests::markup1()],
        ];
    }

    /**
     * @return [string]
     */
    static function CBHTMLOutput_requiredClassNames() {
        return ['CBMessageMarkup'];
    }

    /**
     * @return string
     */
    static function html1() {
        return <<<EOT
<h1>
<p>            This is the Title
</h1>
<p>            This is an
            introductory paragraph.
<ul>
<li>
<p>                This is the first list item.
</li>
<li>
<p>                This is the second list item.
</li>
<li>
<p>                    This is the third list item.
<pre>
Product             Price
-------------- ----------
Socks                2.99
Latte                5.98
Strawberry Jam      14.32
</pre>
<p>                    <em>Prices may vary.</em>
</li>
</ul>
<blockquote>
<p>            A sunny day <br>
            A rainy day <br>
            It doesn&#039;t matter anyway
</blockquote>

EOT;
    }

    /**
     * @return string
     */
    static function markup1() {
        return <<<EOT

            --- h1
            This is the Title
            ---

            This is an
            introductory paragraph.

            --- ul
                This is the first list item.

                This is the second list item.

                --- li
                    This is the third list item.

                    --- pre
Product             Price
-------------- ----------
Socks                2.99
Latte                5.98
Strawberry Jam      14.32
                    ---

                    {em: Prices may vary.}
                ---
            ---

            --- blockquote
            A sunny day {br:}
            A rainy day {br:}
            It doesn't matter anyway
            ---

EOT;
    }

    /**
     * @return null
     */
    static function markupToHTMLTest() {
        $html1 = CBMessageMarkup::markupToHTML(CBMessageMarkupTests::markup1());
        $expectedLines = CBConvert::stringToLines(CBMessageMarkupTests::html1());
        $resultLines = CBConvert::stringToLines($html1);

        array_walk($expectedLines, function ($expectedLine, $index) use ($resultLines) {
            if (!isset($resultLines[$index])) {
                throw new Exception("The result does not have the expected line number {$index}");
            }

            $resultLine = $resultLines[$index];

            if ($expectedLine !== $resultLine) {
                throw new Exception("Line {$index} was expected to be \"{$expectedLine}\" but is actually \"{$resultLine}\"");
            }
        });

        if (count($resultLines) > count($expectedLines)) {
            throw new Exception('The result has more lines than were expected.');
        }
    }
}

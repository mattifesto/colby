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
            ['CBMessageMarkupTests_text1', CBMessageMarkupTests::text1()],
        ];
    }

    /**
     * @return [string]
     */
    static function CBHTMLOutput_requiredClassNames() {
        return ['CBMessageMarkup'];
    }

    /**
     * @param string $string 1
     * @param string $string 2
     *
     * @return void
     */
    static function compareStringsLineByLine(string $string1, string $string2): void {
        $string1Lines = CBConvert::stringToLines($string1);
        $string2Lines = CBConvert::stringToLines($string2);

        array_walk($string1Lines, function ($string1Line, $index) use ($string2Lines) {
            if (!isset($string2Lines[$index])) {
                throw new Exception("Line {$index} of string 1 is \"{$string1Line}\" but doesn't exist in string 2.");
            }

            $string2Line = $string2Lines[$index];

            if ($string1Line !== $string2Line) {
                throw new Exception("String 1 line {$index} is \"{$string1Line}\" which does't match string 2 line {$index} of \"{$string2Line}\"");
            }
        });

        if (count($string2Lines) > count($string1Lines)) {
            throw new Exception('String 2 has more lines than string 1');
        }
    }

    /**
     * @return string
     */
    static function html1(): string {
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
<p>            <strong>Inline Tests</strong>
<ul>
<li>
<p>            The <a href="http://zoo.org">linked</a> word.
</li>
<li>
<p>            The <abbr title="you only live once">YOLO</abbr> abbreviation.
</li>
<li>
<p>            The <b>bold</b> word.
</li>
<li>
<p>            The <bdi>إيان</bdi> name.
</li>
<li>
<p>            The <bdo dir="rtl">right to left</bdo> phrase.
</li>
<li>
<p>            The poetry <br> line break.
</li>
<li>
<p>            The <cite>Good Dog, Carl</cite> citation.
</li>
<li>
<p>            The <code>printf(&quot;Hello, world!&quot;)</code> code.
</li>
<li>
<p>            The product code for <data value="TB3948">Teddy Ruxpin</data>.
</li>
<li>
<p>            The <em>emphasized</em> word.
</li>
<li>
<p>            The <i>italicized</i> word.
</li>
<li>
<p>            The <kbd>Control</kbd> + <kbd>C</kbd> copy.
</li>
<li>
<p>            The <mark>highlighted</mark> word.
</li>
<li>
<p>            The <q>Four score and seven years ago</q> quotation.
</li>
<li>
<p>            The <s>striking</s> strikethrough.
</li>
<li>
<p>            The computer asked me, <samp>Would you like to play a game?</samp>
</li>
<li>
<p>            It was <small>hidden</small> in the small text.
</li>
<li>
<p>            The <strong>strong</strong> word.
</li>
<li>
<p>            I said, <q>take a drink of H<sub>2</sub>O.</q>
</li>
<li>
<p>            He proposed that E=mc<sup>2</sup>.
</li>
<li>
<p>            He was born on <time datetime="1879-03-14">March 14, 1879</time>.
</li>
<li>
<p>            The he wrote <u>The Meaning of Relativity</u>.
</li>
<li>
<p>            I proposed that <var>pizza</var> = <var>dough</var> + <var>love</var>.
</li>
</ul>

EOT;
    }

    /**
     * @return string
     */
    static function markup1(): string {
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

                    (Prices may vary. (em))
                ---
            ---

            --- blockquote
            A sunny day ((br))
            A rainy day ((br))
            It doesn't matter anyway
            ---

            (Inline Tests (strong))

            --- ul
            The (linked (a http://zoo.org)) word.

            The (  YOLO  (  abbr you only live once  )  ) abbreviation.

            The (bold (b)) word.

            The (إيان (bdi)) name.

            The (right to left (bdo rtl)) phrase.

            The poetry ((br)) line break.

            The (Good Dog, Carl (cite)) citation.

            The (printf\("Hello, world!"\) (code)) code.

            The product code for (Teddy Ruxpin (data TB3948)).

            The (emphasized (em)) word.

            The (italicized (i)) word.

            The (Control (kbd)) + (C (kbd)) copy.

            The (highlighted (mark)) word.

            The (Four score and seven years ago (q)) quotation.

            The (striking (s)) strikethrough.

            The computer asked me, (Would you like to play a game? (samp))

            It was (hidden (small)) in the small text.

            The (strong (strong)) word.

            I said, (take a drink of H(2 (sub))O. (q))

            He proposed that E=mc(2 (sup)).

            He was born on (March 14, 1879 (time 1879-03-14)).

            The he wrote (The Meaning of Relativity (u)).

            I proposed that (pizza (var)) = (dough (var)) + (love (var)).
            ---
EOT;
    }

    /**
     * @return void
     */
    static function markupToHTMLTest(): void {
        $expected = CBMessageMarkupTests::html1();
        $result = CBMessageMarkup::markupToHTML(CBMessageMarkupTests::markup1());

        CBMessageMarkupTests::compareStringsLineByLine($expected, $result);
    }

    /**
     * @return void
     */
    static function markupToTextTest(): void {
        $expected = CBMessageMarkupTests::text1();
        $result = CBMessageMarkup::markupToText(CBMessageMarkupTests::markup1());

        CBMessageMarkupTests::compareStringsLineByLine($expected, $result);
    }

    /**
     * @return void
     */
    static function stringToMarkupTest(): void {
        $string = <<<EOT
--- div
--- hi
    ---
(hi (strong))
( \( \\( \\\(
) \) \\) \\\)
EOT;

        $expected = <<<EOT
\-\-\- div
\-\-\- hi
    \-\-\-
\(hi \(strong\)\)
\( \\\( \\\( \\\\\(
\) \\\) \\\) \\\\\)
EOT;

        $result = CBMessageMarkup::stringToMarkup($string);

        CBMessageMarkupTests::compareStringsLineByLine($expected, $result);
    }

    static function text1(): string {
        return <<<EOT
            This is the Title

            This is an
            introductory paragraph.

                This is the first list item.

                This is the second list item.

                    This is the third list item.

Product             Price
-------------- ----------
Socks                2.99
Latte                5.98
Strawberry Jam      14.32

                    Prices may vary.

            A sunny day
            A rainy day
            It doesn't matter anyway

            Inline Tests

            The linked word.

            The YOLO abbreviation.

            The bold word.

            The إيان name.

            The right to left phrase.

            The poetry  line break.

            The Good Dog, Carl citation.

            The printf("Hello, world!") code.

            The product code for Teddy Ruxpin.

            The emphasized word.

            The italicized word.

            The Control + C copy.

            The highlighted word.

            The Four score and seven years ago quotation.

            The striking strikethrough.

            The computer asked me, Would you like to play a game?

            It was hidden in the small text.

            The strong word.

            I said, take a drink of H2O.

            He proposed that E=mc2.

            He was born on March 14, 1879.

            The he wrote The Meaning of Relativity.

            I proposed that pizza = dough + love.

EOT;
    }
}

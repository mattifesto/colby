<?php

final class CBMessageMarkupTests {

    /* -- CBHTMLOutput interfaces -- -- -- -- -- */

    /**
     * @return [string]
     */
    static function CBHTMLOutput_JavaScriptURLs() {
        return [
            Colby::flexpath(__CLASS__, 'v469.js', cbsysurl()),
        ];
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
        return [
            'CBConvert',
            'CBMessageMarkup'
        ];
    }

    /* -- CBTest interfaces -- -- -- -- -- */

    /**
     * @return [[<className>, <testName>]]
     */
    static function CBTest_JavaScriptTests(): array {
        return [
            ['CBMessageMarkup', 'messageToHTML'],
            ['CBMessageMarkup', 'messageToText'],
            ['CBMessageMarkup', 'singleLineMarkupToText'],
            ['CBMessageMarkup', 'stringToMarkup'],
        ];
    }

    /**
     * @return [[<class>, <test>]]
     */
    static function CBTest_PHPTests(): array {
        return [
            ['CBMessageMarkup', 'messageToHTML'],
            ['CBMessageMarkup', 'messageToText'],
            ['CBMessageMarkup', 'paragraphToText'],
            ['CBMessageMarkup', 'singleLineMarkupToText'],
            ['CBMessageMarkup', 'stringToMarkup'],
        ];
    }

    /* -- functions -- -- -- -- -- */

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
            This is the Title
</h1>
<p>            This is an
            introductory paragraph.
<p>            ( \\( \\\\( ) \\) \\\\)
<p>            Explicitly declared p
<ul>
<li>
<p>                This is the first list item.
</li>
<li>
<p>                This is the second list item which is a long sentence that will
                not wrap if it is converted to text.
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
<div class="one two three four">
<p>            This is an unspecified div.
</div>
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

            \( \\\\\\( \\\\\\\\\( \) \\\\\) \\\\\\\\\)

            --- p
            Explicitly declared p
            ---

            --- ul
                This is the first list item.

                This is the second list item which is a long sentence that will
                not wrap if it is converted to text.

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

            --- one two three four
            This is an unspecified div.
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
     * @param string $string 1
     * @param string $string 2
     *
     * @return void
     */
    static function textResultMismatchFailure(
        string $testTitle,
        string $actualResult,
        string $expectedResult
    ): stdClass {
        $actualResultLines = CBConvert::stringToLines($actualResult);
        $expectedResultLines = CBConvert::stringToLines($expectedResult);

        for ($index = 0; $index < count($actualResultLines); $index += 1) {
            $lineNumber = $index + 1;
            $actualResultLine = $actualResultLines[$index];

            if (!isset($expectedResultLines[$index])) {
                $message = <<<EOT

                    The actual result has more lines than the expected result.

EOT;
                break;
            }

            $expectedResultLine = $expectedResultLines[$index];

            if ($actualResultLine !== $expectedResultLine) {
                $actualResultLineAsMessage = CBMessageMarkup::stringToMessage(
                    $actualResultLine
                );

                $expectedResultLineAsMessage = CBMessageMarkup::stringToMessage(
                    $expectedResultLine
                );

                $message = <<<EOT

                    Line {$lineNumber} of the actual result does not match the
                    expected result.

                    --- dl
                        --- dt
                        actual line
                        ---
                        --- dd
                            --- pre\n{$actualResultLineAsMessage}
                            ---
                        ---
                        --- dt
                        expected line
                        ---
                        --- dd
                            --- pre\n{$expectedResultLineAsMessage}
                            ---
                        ---
                    ---

EOT;

                break;
            }
        }

        if (
            !isset($message) &&
            count($actualResultLines) < count($expectedResultLines)
        ) {
            $message = <<<EOT

                The actual result has less lines than the expected result.

EOT;
        }

        $actualResultAsMessage = CBMessageMarkup::stringToMessage(
            $actualResult
        );
        $expectedResultAsMessage = CBMessageMarkup::stringToMessage(
            $expectedResult
        );
        $message = <<<EOT

            {$message}

            --- dl
                --- dt
                Test Title
                ---
                {$testTitle}

                --- dt
                Actual Result
                ---
                --- dd
                    --- pre\n{$actualResultAsMessage}
                    ---
                ---

                --- dt
                Expected Result
                ---
                --- dd
                    --- pre\n{$expectedResultAsMessage}
                    ---
                ---
            ---

EOT;

        return (object)[
            'succeeded' => false,
            'message' => $message,
        ];
    }

    /* -- CBTest interfaces -- -- -- -- -- */

    /**
     * @return object
     */
    static function CBTest_messageToHTML(): stdClass {
        $actualResult = CBMessageMarkup::messageToHTML(
            CBMessageMarkupTests::markup1()
        );
        $expectedResult = CBMessageMarkupTests::html1();

        if ($actualResult !== $expectedResult) {
            return CBMessageMarkupTests::textResultMismatchFailure(
                'tests 1',
                $actualResult,
                $expectedResult
            );
        } else {
            return (object)[
                'succeeded' => 'true',
            ];
        }
    }

    /**
     * @return object
     */
    static function CBTest_messageToText(): stdClass {
        $expectedResult = CBMessageMarkupTests::text1();
        $actualResult = CBMessageMarkup::messageToText(
            CBMessageMarkupTests::markup1()
        );

        if ($actualResult !== $expectedResult) {
            return CBMessageMarkupTests::textResultMismatchFailure(
                'test 1',
                $actualResult,
                $expectedResult
            );
        } else {
            return (object)[
                'succeeded' => 'true',
            ];
        }
    }

    /**
     * @return object
     */
    static function CBTest_paragraphToText(): stdClass {
        $message = <<<EOT

            This is a
            paragraph that is
                spaced
            oddly
                and is great. Also it is more than eighty
            characters
                in length for heavens sake!

EOT;
        $expectedResult = <<<EOT
This is a paragraph that is spaced oddly and is great. Also it is more than
eighty characters in length for heavens sake!
EOT;

        $actualResult = CBMessageMarkup::paragraphToText($message);

        if ($actualResult !== $expectedResult) {
            return CBMessageMarkupTests::textResultMismatchFailure(
                'test 1',
                $actualResult,
                $expectedResult
            );
        } else {
            return (object)[
                'succeeded' => 'true',
            ];
        }
    }

    /**
     * @NOTE
     *
     *      CBMessageMarkup::messageToText() always puts a new line at the end of
     *      the last line whether one was originally there or not.
     *
     * @return object
     */
    static function CBTest_singleLineMarkupToText(): stdClass {
        $singleLineMarkup = 'This \(is \- the - result)!';
        $expected = "This (is - the - result)!\n";
        $result = CBMessageMarkup::messageToText($singleLineMarkup);

        CBMessageMarkupTests::compareStringsLineByLine($expected, $result);

        $singleLineMarkup = 'This is an ID: (68658b6709f44bf11248a88975486ea6bac7ef60 (code))';
        $actualResult = CBMessageMarkup::messageToText($singleLineMarkup);
        $expectedResult = "This is an ID: 68658b6709f44bf11248a88975486ea6bac7ef60\n";

        if ($actualResult != $expectedResult) {
            return CBTest::resultMismatchFailureDiff('Subtest 2', $actualResult, $expectedResult);
        }

        return (object)[
            'succeeded' => 'true',
        ];
    }

    /**
     * @return object
     */
    static function CBTest_stringToMarkup(): stdClass {
        $string = <<<EOT
--- div
--- hi
    ---
(hi (strong))
( \\( \\\\(
) \\) \\\\)
EOT;

        $expected = <<<EOT
\\-\\-\\- div
\\-\\-\\- hi
    \\-\\-\\-
\\(hi \\(strong\\)\\)
\\( \\\\\\( \\\\\\\\\\(
\\) \\\\\\) \\\\\\\\\\)
EOT;

        $result = CBMessageMarkup::stringToMarkup($string);

        CBMessageMarkupTests::compareStringsLineByLine($expected, $result);

        return (object)[
            'succeeded' => 'true',
        ];
    }

    /**
     * @return string
     */
    static function text1(): string {
        return <<<EOT
This is the Title

This is an introductory paragraph.

( \( \\\\( ) \) \\\\)

Explicitly declared p

This is the first list item.

This is the second list item which is a long sentence that will not wrap if it is converted to text.

This is the third list item.

Product Price -------------- ---------- Socks 2.99 Latte 5.98 Strawberry Jam 14.32

Prices may vary.

A sunny day A rainy day It doesn't matter anyway

This is an unspecified div.

Inline Tests

The linked word.

The YOLO abbreviation.

The bold word.

The إيان name.

The right to left phrase.

The poetry line break.

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

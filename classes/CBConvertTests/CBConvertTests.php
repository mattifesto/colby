<?php

final class CBConvertTests {

    /**
     * return void
     */
    static function linesToParagraphsTest() {
        $lines      = ['a', ' b ', ' ', '    ', '', '    c', '  d'];
        $expected   = ['a  b ', '    c   d'];
        $actual     = ColbyConvert::linesToParagraphs($lines);
        $diff       = array_diff($actual, $expected);

        if ($diff) {
            $JSON = json_encode($diff);
            throw new RuntimeException("runTestForLineArrayToParagraphArray: The array returned does not match the expected array with these differences: {$JSON}.");
        }
    }

    /**
     * return void
     */
    static function textToLinesTest() {
        $text       = "abc \r bcd \n cde \r\n def \n\r efg \r\n\r fgh \r\n\n ghi \r\r\n hij \r\n\r\n ijk";
        $expected   = ['abc ', ' bcd ', ' cde ', ' def ',
                       '', ' efg ', '', ' fgh ', '', ' ghi ', '', ' hij ', '', ' ijk'];
        $actual     = ColbyConvert::textToLines($text);
        $diff       = array_diff($actual, $expected);

        if ($diff) {
            $JSON = json_encode($diff);
            throw new RuntimeException("runTestForTextToLineArray: The array returned does not match the expected array with these differences: {$JSON}.");
        }
    }

    /**
     * @return null
     */
    static function textToStubTest() {
        $text = ' ';
        $stub = ColbyConvert::textToStub($text);

        if ($stub !== '') {
            throw new Exception('Test failed');
        }
    }
}

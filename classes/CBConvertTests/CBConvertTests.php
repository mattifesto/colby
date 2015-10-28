<?php

final class CBConvertTests {

    /**
     * @return null
     */
    public static function textToStubTest() {
        $text = ' ';
        $stub = ColbyConvert::textToStub($text);

        if ($stub !== '') {
            throw new Exception('Test failed');
        }
    }
}

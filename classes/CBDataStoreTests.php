<?php

class CBDataStoreTests {

    /**
     * @return null
     */
    public static function directoryNameFromDocumentRootTest() {
        $IDs[]      = 'ee06b609529f624ad6491c5b83fd6daa1387dc7c';
        $IDs[]      = '4F122d0cb0504937a60111f370a06E2b9306fb9D';
        $IDs[]      = strtoupper('254f4ba1bf8d8690a993fbf0b03e263202726d74');
        $expected[] = 'data/ee/06/b609529f624ad6491c5b83fd6daa1387dc7c';
        $expected[] = 'data/4f/12/2d0cb0504937a60111f370a06e2b9306fb9d';
        $expected[] = 'data/25/4f/4ba1bf8d8690a993fbf0b03e263202726d74';

        $actual         = array_map('CBDataStore::directoryNameFromDocumentRoot', $IDs);
        $expectedOnly   = implode(',', array_diff($expected, $actual));
        $actualOnly     = implode(',', array_diff($actual, $expected));

        if ($expectedOnly || $actualOnly) {
            throw new Exception(
                "The expected array and the actual array don't match. " .
                "Items only in expected: {$expectedOnly} Items only in actual: {$actualOnly}");
        }
    }

    /**
     * @return null
     */
    public static function toURLTest() {
        $ID         = '25c4a69a256a778ff892c60779a31ee1025b1e68';
        $URL        = CBDataStore::toURL(['ID' => $ID]);
        $expected   = CBSiteURL . '/data/25/c4/a69a256a778ff892c60779a31ee1025b1e68';

        if ($URL !== $expected) {
            $a = json_encode($URL);
            $e = json_encode($expected);
            throw new Exception("The actual URL: {$a} does not match the expected URL: {$e}");
        }

        $URL        = CBDataStore::toURL(['ID' => $ID, 'filename' => 'hello.jpg']);
        $expected   = CBSiteURL . '/data/25/c4/a69a256a778ff892c60779a31ee1025b1e68/hello.jpg';

        if ($URL !== $expected) {
            $a = json_encode($URL);
            $e = json_encode($expected);
            throw new Exception("The actual URL: {$a} does not match the expected URL: {$e}");
        }
    }
}

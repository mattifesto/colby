<?php

class CBDataStoreTests {

    /* -- CBTest interfaces -- -- -- -- -- */



    /**
     * @return [object]
     */
    static function CBTest_getTests(): array {
        return [
            (object)[
                'name' => 'createAndDelete',
                'type' => 'server',
            ],
            (object)[
                'name' => 'directoryNameFromDocumentRoot',
                'type' => 'server',
            ],
            (object)[
                'name' => 'toURL',
                'type' => 'server',
            ],
            (object)[
                'name' => 'URIToFilepath',
                'type' => 'server',
            ],
            (object)[
                'name' => 'URIToID',
                'type' => 'server',
            ],
        ];
    }
    /* CBTest_getTests() */



    /* -- tests -- -- -- -- -- */



    /**
     * @return object
     */
    static function
    CBTest_URIToFilepath(
    ): stdClass
    {
        /**
         * This is a real class and we are going to user a file inside the class
         * directory for these tests.
         */
        $testClassName =
        'CBDataStore';

        /**
         * It's important that these tests test some URLs with a domain but it
         * doesn't matter what that domain is. A website can have multiple
         * domains and they may be known only by the load balancer so it doesn't
         * matter which specific domain we use in these tests, even if we are
         * pretty sure it isn't actually ever used.
         */
        $testDomain =
        'example.com';


        $relativePathFromDocumentRootToColbyLibrary =
        CB_Directories::getRelativePathFromDocumentRootToColbyLibrary();

        $relativePathFromColbyLibraryToClassFile =
        CBLibrary::buildLibraryClassFilePath(
            $testClassName,
            '',
            'php',
            ''
        );

        // the error here is that $relative is not a valid file path
        //$relative = '/colby/classes/CBDataStore/CBDataStore.php';
        $relativePathFromDocumentRootToClassFile =
        $relativePathFromDocumentRootToColbyLibrary .
        '/' .
        $relativePathFromColbyLibraryToClassFile;

        $filepath =
        cbsitedir() .
        '/' .
        $relativePathFromDocumentRootToClassFile;

        $cases =
        [
            [
                cbsiteurl() . "/$relativePathFromDocumentRootToClassFile",
                $filepath,
            ],
            [
                "/$relativePathFromDocumentRootToClassFile",
                $filepath,
            ],
            [
                "//$testDomain/$relativePathFromDocumentRootToClassFile",
                $filepath,
            ],
            [
                "http://$testDomain/$relativePathFromDocumentRootToClassFile",
                $filepath,
            ],
            [
                "https://$testDomain/$relativePathFromDocumentRootToClassFile",
                $filepath,
            ],
            [
                $relativePathFromDocumentRootToClassFile,
                $filepath,
                'ad0bfbb5d9459185107cf3b158c3b3d0209c7f2d',
            ],
            [
                "http://$testDomain",
                null
            ],
            [
                "http://$testDomain/",
                null,
            ],
            [
                '',
                null,
            ],
            [
                '/foo/bar/',
                null,
            ],
        ];

        for (
            $index = 0;
            $index < count($cases);
            $index += 1
        ) {
            $case = $cases[$index];

            try
            {
                $actualResult =
                CBDataStore::URIToFilepath(
                    $case[0]
                );
            }
            catch (
                Throwable $throwable
            ) {
                $actualSourceCBID =
                CBException::throwableToSourceCBID(
                    $throwable
                );

                $expectedSourceCBID =
                $case[2];

                if (
                    $actualSourceCBID === $expectedSourceCBID
                ) {
                    continue;
                }

                throw $throwable;
            }

            $expectedResult = $case[1];

            if (
                $actualResult !== $expectedResult
            ) {
                return CBTest::resultMismatchFailure(
                    "test index {$index}",
                    $actualResult,
                    $expectedResult
                );
            }
        }

        return (object)[
            'succeeded' => true,
        ];
    }
    /* CBTest_URIToFilepath() */



    /**
     * @return object
     */
    static function CBTest_createAndDelete(): stdClass {
        $ID = 'de7c294f327940e6c18fc3b7e6020c8c7046c95b';

        CBDataStore::deleteByID($ID);
        CBDataStoreTests::confirmDeleted($ID);

        CBDataStore::create($ID);
        CBDataStoreTests::confirmExists($ID);

        CBDataStore::deleteByID($ID);
        CBDataStoreTests::confirmDeleted($ID);

        return (object)[
            'succeeded' => true,
        ];
    }
    /* CBTest_createAndDelete() */



    /**
     * @return object
     */
    static function CBTest_directoryNameFromDocumentRoot(): stdClass {
        $IDs[]      = 'ee06b609529f624ad6491c5b83fd6daa1387dc7c';
        $IDs[]      = '4F122d0cb0504937a60111f370a06E2b9306fb9D';
        $IDs[]      = strtoupper('254f4ba1bf8d8690a993fbf0b03e263202726d74');
        $expected[] = 'data/ee/06/b609529f624ad6491c5b83fd6daa1387dc7c';
        $expected[] = 'data/4f/12/2d0cb0504937a60111f370a06e2b9306fb9d';
        $expected[] = 'data/25/4f/4ba1bf8d8690a993fbf0b03e263202726d74';

        $actual = array_map(
            'CBDataStore::directoryNameFromDocumentRoot',
            $IDs
        );

        $expectedOnly   = implode(',', array_diff($expected, $actual));
        $actualOnly     = implode(',', array_diff($actual, $expected));

        if ($expectedOnly || $actualOnly) {
            throw new Exception(
                "The expected array and the actual array don't match. " .
                "Items only in expected: {$expectedOnly} Items only " .
                "in actual: {$actualOnly}"
            );
        }

        return (object)[
            'succeeded' => true,
        ];
    }
    /* CBTest_directoryNameFromDocumentRoot() */



    /**
     * @return object
     */
    static function CBTest_toURL(): stdClass {
        $ID         = '25c4a69a256a778ff892c60779a31ee1025b1e68';
        $URL        = CBDataStore::toURL(['ID' => $ID]);

        $expected = (
            cbsiteurl() . '/data/25/c4/a69a256a778ff892c60779a31ee1025b1e68'
        );

        if ($URL !== $expected) {
            $a = json_encode($URL);
            $e = json_encode($expected);

            throw new Exception(
                "The actual URL: {$a} does not match the expected URL: {$e}"
            );
        }

        $URL = CBDataStore::toURL(['ID' => $ID, 'filename' => 'hello.jpg']);

        $expected = (
            cbsiteurl() .
            '/data/25/c4/a69a256a778ff892c60779a31ee1025b1e68/hello.jpg'
        );

        if ($URL !== $expected) {
            $a = json_encode($URL);
            $e = json_encode($expected);

            throw new Exception(
                "The actual URL: {$a} does not match the expected URL: {$e}"
            );
        }

        return (object)[
            'succeeded' => true,
        ];
    }
    /* CBTest_toURL() */



    /**
     * @return object
     */
    static function CBTest_URIToID(): stdClass {
        $ID = CBID::generateRandomCBID();
        $path = CBDataStore::directoryNameFromDocumentRoot($ID);
        $siteDomainName = CBSitePreferences::siteDomainName();
        $siteDirectory = CBSitePreferences::siteDirectory();

        $tests = [
            ["/{$path}/", $ID],
            ["/$path", false],
            ["/ff/{$path}/", false],
            ["http://{$siteDomainName}/{$path}/test.txt", $ID],
            ["https://{$siteDomainName}:8080/{$path}/thumbnail.jpg", $ID],
            ["ftp://{$siteDomainName}/{$path}/download.png", $ID],
            ["//{$siteDomainName}/{$path}/", $ID],
            ["{$siteDirectory}/{$path}/sub/dir/data.json", $ID],
            ["http://apple.com/{$path}/main.html", false],
        ];

        foreach ($tests as $test) {
            $value = CBDataStore::URIToID($test[0]);

            if ($value !== $test[1]) {
                $v = json_encode($value);
                $e = json_encode($test[1]);

                throw new Exception(
                    "The returned value for the URI: " .
                    "'{$test[0]}' was '{$v}'. The expected value was '{$e}'"
                );
            }
        }

        return (object)[
            'succeeded' => true,
        ];
    }
    /* CBTest_URIToID() */



    /* -- functions -- -- -- -- */



    /**
     * @return null
     */
    private static function confirmDeleted($ID) {
        $IDAsSQL = CBID::toSQL($ID);
        $count = CBDB::SQLToValue(
            "SELECT COUNT(*) FROM `CBDataStores` WHERE `ID` = {$IDAsSQL}"
        );

        if ($count != 0) {
            throw new Exception("A CBDataStores row exists for the ID: {$ID}");
        }

        $directory = CBDataStore::flexpath($ID, null, cbsitedir());

        if (is_dir($directory)) {
            throw new Exception(
                "A data store directory exists for the ID: {$ID}"
            );
        }
    }



    /**
     * @return null
     */
    private static function confirmExists($ID) {
        $IDAsSQL = CBID::toSQL($ID);
        $count = CBDB::SQLToValue(
            "SELECT COUNT(*) FROM `CBDataStores` WHERE `ID` = {$IDAsSQL}"
        );

        if ($count != 1) {
            throw new Exception(
                "A CBDataStores row does not exist for the ID: {$ID}"
            );
        }

        $directory = CBDataStore::flexpath($ID, null, cbsitedir());

        if (!is_dir($directory)) {
            throw new Exception(
                "A data store directory does not exist for the ID: {$ID}"
            );
        }
    }

}

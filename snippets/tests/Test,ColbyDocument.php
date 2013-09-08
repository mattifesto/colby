<?php

include_once COLBY_SYSTEM_DIRECTORY . '/classes/ColbyDocument.php';


/**
 * Run tests.
 */

ColbyDocumentTests::archiveConversionTest();
ColbyDocumentTests::testDeleteDocumentWithArchiveId();
ColbyDocumentTests::testSave();
ColbyDocumentTests::testSetURI();


/**
 *
 */
class ColbyDocumentTests
{
    /**
     * This function tests loading an archive that was created before the
     * ColbyDocument class existed to make sure the archive is properly
     * updated to include the 'documentRowId' value and that it's correctly set.
     *
     * @return void
     */
    public static function archiveConversionTest()
    {
        $testArchiveId = 'a2676eae5c9fff9901cf89afb511d5dda0f6e206';

        $archiveDirectory = ColbyArchive::absoluteDataDirectoryForArchiveId($testArchiveId);
        $archiveDataFilename = "{$archiveDirectory}/archive.data";

        /**
         * If the test failed last time parts of the document may still exist
         * so delete them to prepare for this test.
         */

        ColbyDocument::deleteDocumentWithArchiveId($testArchiveId);

        /**
         * Prepare the document row for the test.
         */

        $safeArchiveId = Colby::mysqli()->escape_string($testArchiveId);

        $sql = <<<EOT
INSERT INTO `ColbyPages`
    (`archiveId`, `stub`, `titleHTML`, `subtitleHTML`)
VALUES
    (UNHEX('{$safeArchiveId}'), '{$safeArchiveId}', '', '')
EOT;

        Colby::query($sql);

        $documentRowId = intval(Colby::mysqli()->insert_id);

        if (!self::documentRowDoesExistForArchiveId($testArchiveId))
        {
            throw new RuntimeException('At this point, a document row should exist for the test archive id.');
        }

        /**
         * Prepare the archive for the test.
         */

        $archive = ColbyArchive::open($testArchiveId);

        $testKey = 'testKey';
        $testValue = "Hello, world!";

        $archive->setStringValueForKey($testValue, $testKey);

        $archive->save();

        $archive = null;

        /**
         * Create a ColbyDocument instance an make sure the 'documentRowId'
         * value gets set correctly.
         */

        $document = ColbyDocument::documentWithArchiveId($testArchiveId);

        if ($document->archive()->valueForKey('documentRowId') !== $documentRowId)
        {
            throw new RuntimeException('The document row id was not set correctly.');
        }

        /**
         * Verify that our test value is still set and that the document
         * conversion didn't remove the existing values.
         */

        if ($document->archive()->valueForKey($testKey) !== $testValue)
        {
            throw new RuntimeException('The test value in the archive is not correct.');
        }

        $document = null;

        /**
         * Delete the test document.
         */

        ColbyDocument::deleteDocumentWithArchiveId($testArchiveId);
    }

    /**
     * @return bool
     */
    private static function documentRowDoesExistForArchiveId($archiveId)
    {
        $safeArchiveId = Colby::mysqli()->escape_string($archiveId);

        $sql = "SELECT COUNT(*) as `count` FROM `ColbyPages` WHERE `archiveId` = UNHEX('{$safeArchiveId}')";

        $result = Colby::query($sql);

        $documentRowDoesExistForArchiveId = ($result->fetch_object()->count == 1);

        $result->free();

        return $documentRowDoesExistForArchiveId;
    }

    /**
     * This function tests the `ColbyDocument::deleteDocumentWithArchiveId`
     * method. This function only tests the ColbyDocument specific
     * functionality. The ColbyArchive functionality used by ColbyDocument
     * is tested in the ColbyArchive tests.
     *
     * @return void
     */
    public static function testDeleteDocumentWithArchiveId()
    {
        $testArchiveId = 'c30b6e5cbe9c0afc179354d5047008a83e462e19';

        $archiveDirectory = ColbyArchive::absoluteDataDirectoryForArchiveId($testArchiveId);
        $archiveDataFilename = "{$archiveDirectory}/archive.data";

        /**
         * Just in case the test failed the last time it ran, delete the test
         * document and verify that there is no row or archive directory for
         * the document.
         */

        ColbyDocument::deleteDocumentWithArchiveId($testArchiveId);

        if (self::documentRowDoesExistForArchiveId($testArchiveId))
        {
            throw new RuntimeException('At this point, no document row should exist for the test archive id.');
        }

        if (file_exists($archiveDirectory))
        {
            throw new RuntimeException('At this point, no archive directory should exist for the test archive id.');
        }

        /**
         * Create the test document and verify that it has a row and and an
         * archive data file.
         */

        $document = ColbyDocument::documentWithArchiveId($testArchiveId);

        $document = null;

        if (!self::documentRowDoesExistForArchiveId($testArchiveId))
        {
            throw new RuntimeException('At this point, a document row should exist for the test archive id.');
        }

        if (!file_exists($archiveDataFilename))
        {
            throw new RuntimeException('At this point, an archive data file should exist for the test archive id.');
        }

        /**
         * Delete the document and verify that the document row and the archive
         * directory have been removed.
         */

        ColbyDocument::deleteDocumentWithArchiveId($testArchiveId);

        if (self::documentRowDoesExistForArchiveId($testArchiveId))
        {
            throw new RuntimeException('At this point, no document row should exist for the test archive id.');
        }

        if (file_exists($archiveDirectory))
        {
            throw new RuntimeException('At this point, no archive directory should exist for the test archive id.');
        }
    }

    /**
     * This function tests the `ColbyDocument->save` method. It only tests the
     * ColbyDocument specific parts of the method. The ColbyArchive parts are
     * tested by the ColbyArchive tests.
     *
     * @return void
     */
    public static function testSave()
    {
        $testArchiveId = 'a3bb3ec44038e15274c1956a286486d6997c3bf1';

        $documentGroupId = '08881dc1d8c5d6d06c7d42dc02642982b5e54eea';
        $documentTypeId = 'e9cd479235c73c4f77f8bea0f4e2ba3a5cde1cce';
        $titleHTML = 'Jan &amp; Dean';
        $subtitleHTML = 'Surfing &amp; Singing';
        $thumbnailURL = 'http://example.com/thumbnail.jpg';
        $searchText = 'Jan & Dean Surfing & Singing';
        $publishedTimeStamp = time();
        $publishedBy = ColbyUser::currentUserId();


        /**
         * If the test failed last time parts of the document may still exist
         * so delete them to prepare for this test.
         */

        ColbyDocument::deleteDocumentWithArchiveId($testArchiveId);

        /**
         * Create a document an set the appropriate values to be saved to the
         * document row.
         */

        $document = ColbyDocument::documentWithArchiveId($testArchiveId);
        $archive = $document->archive();

        $archive->setStringValueForKey($documentGroupId, 'documentGroupId');
        $archive->setStringValueForKey($documentTypeId, 'documentTypeId');
        $archive->setStringValueForKey($titleHTML, 'titleHTML');
        $archive->setStringValueForKey($subtitleHTML, 'subtitleHTML');
        $archive->setStringValueForKey($thumbnailURL, 'thumbnailURL');
        $archive->setStringValueForKey($searchText, 'searchText');
        $archive->setBoolValueForKey(true, 'isPublished');
        $archive->setIntValueForKey($publishedTimeStamp, 'publishedTimeStamp');
        $archive->setIntValueForKey($publishedBy, 'publishedBy');

        $document->save();

        $safeDocumentRowId = (int)$archive->valueForKey('documentRowId');

        $document = null;
        $archive = null;

        /**
         * Query the database directly and compare the values that should have
         * been saved.
         */

        $sql = <<<EOT
SELECT
    LOWER(HEX(`archiveId`)) as `archiveId`,
    LOWER(HEX(`groupId`)) as `documentGroupId`,
    LOWER(HEX(`modelId`)) as `documentTypeId`,
    `titleHTML`,
    `subtitleHTML`,
    `thumbnailURL`,
    `searchText`,
    `published`,
    `publishedBy`
FROM
    `ColbyPages`
WHERE
    `id` = {$safeDocumentRowId}
EOT;

        $result = Colby::query($sql);

        $row = $result->fetch_object();

        $result->free();

        if ($row->archiveId !== $testArchiveId)
        {
            throw new RuntimeException('The row value for \'archiveId\' does not match the archive value.');
        }

        if ($row->documentGroupId !== $documentGroupId)
        {
            throw new RuntimeException('The row value for \'documentGroupId\' does not match the archive value.');
        }

        if ($row->documentTypeId !== $documentTypeId)
        {
            throw new RuntimeException('The row value for \'documentTypeId\' does not match the archive value.');
        }

        if ($row->titleHTML !== $titleHTML)
        {
            throw new RuntimeException('The row value for \'titleHTML\' does not match the archive value.');
        }

        if ($row->subtitleHTML !== $subtitleHTML)
        {
            throw new RuntimeException('The row value for \'subtitleHTML\' does not match the archive value.');
        }

        if ($row->thumbnailURL !== $thumbnailURL)
        {
            throw new RuntimeException('The row value for \'thumbnailURL\' does not match the archive value.');
        }

        if ($row->searchText !== $searchText)
        {
            throw new RuntimeException('The row value for \'searchText\' does not match the archive value.');
        }

        if ($row->published != $publishedTimeStamp)
        {
            throw new RuntimeException('The row value for \'publishedTimeStamp\' does not match the archive value.');
        }

        if ($row->publishedBy != $publishedBy)
        {
            throw new RuntimeException('The row value for \'publishedBy\' does not match the archive value.');
        }

        /**
         * Delete the test document.
         */

        ColbyDocument::deleteDocumentWithArchiveId($testArchiveId);
    }

    /**
     * @return void
     */
    public static function testSetURI()
    {
        $testArchiveId1 = 'bea45060ec5cc3bca2be60a16ce0a3b50b3fdae0';
        $testArchiveId2 = '55279087e66edd74403a7a6894f18a94a739d7d7';

        $testURI = 'tests/colby-document/test-set-uri';

        /**
         * If the test failed last time parts of the documents may still exist
         * so delete them to prepare for this test.
         */

        ColbyDocument::deleteDocumentWithArchiveId($testArchiveId1);
        ColbyDocument::deleteDocumentWithArchiveId($testArchiveId2);

        /**
         * Create the first document and set the URI.
         */

        $document1 = ColbyDocument::documentWithArchiveId($testArchiveId1);
        $safeDocumentRowId1 = (int)$document1->archive()->valueForKey('documentRowId');

        $document1->setURI($testURI);
        $document1->save();

        if ($document1->archive()->valueForKey('uri') !== $testURI)
        {
            throw new RuntimeException('The value for the "uri" key does not match the value set.');
        }

        $document1 = null;

        /**
         * Verify that the URI saved to the database matches the value that
         * was set.
         */

        $sql = "SELECT `stub` FROM `ColbyPages` WHERE `id` = {$safeDocumentRowId1}";

        $result = Colby::query($sql);

        $uri = $result->fetch_object()->stub;

        $result->free();

        if ($uri !== $testURI)
        {
            throw new RuntimeException('The uri in the database row does not match the value set.');
        }

        /**
         * Create the second document and verify that an error is thrown when
         * attempting to set the same URI.
         */

        $document2 = ColbyDocument::documentWithArchiveId($testArchiveId2);
        $safeDocumentRowId2 = (int)$document2->archive()->valueForKey('documentRowId');

        $testWasSuccessful = false;

        try
        {
            $document2->setURI($testURI);
        }
        catch (Exception $exception)
        {
            if (1062 == Colby::mysqli()->errno)
            {
                $testWasSuccessful = true;
            }
        }

        if (!$testWasSuccessful)
        {
            throw new RuntimeException('The correct behavior was not observed when attempting to set a URI which is already in use for a document.');
        }

        $document2->save();

        if ($document2->archive()->valueForKey('uri') !== $testArchiveId2)
        {
            throw new RuntimeException('The value for the "uri" key does not match the default value set byt the ColbyDocument class.');
        }

        $document2 = null;

        /**
         * Delete the test documents.
         */

        ColbyDocument::deleteDocumentWithArchiveId($testArchiveId1);
        ColbyDocument::deleteDocumentWithArchiveId($testArchiveId2);
    }
}

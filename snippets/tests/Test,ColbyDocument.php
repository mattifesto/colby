<?php

include_once COLBY_DIRECTORY . '/classes/ColbyDocument.php';


/**
 * Run tests.
 */

ColbyDocumentTests::newDocumentTest();
ColbyDocumentTests::archiveConversionTest();


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
         * Make sure that no document data exists before we start the tests.
         */

        if (self::documentRowDoesExistForArchiveId($testArchiveId))
        {
            throw new RuntimeException('At this point, no document row should exist for the test archive id.');
        }

        if (file_exists($archiveDirectory))
        {
            throw new RuntimeException('At this point, no archive directory should exist for the test archive id.');
        }

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

        $archive->save();

        $archive = null;

        if (!file_exists($archiveDataFilename))
        {
            throw new RuntimeException('At this point, an archive data file should exist for the test archive id.');
        }

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
         * Delete the test document.
         */

        self::deleteDocumentAndVerifyForArchiveId($testArchiveId);
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
     * @return void
     */
    private static function deleteDocumentAndVerifyForArchiveId($archiveId)
    {
        $archiveDirectory = ColbyArchive::absoluteDataDirectoryForArchiveId($archiveId);

        ColbyDocument::deleteDocumentWithArchiveId($archiveId);

        if (self::documentRowDoesExistForArchiveId($archiveId))
        {
            throw new RuntimeException('At this point, no document row should exist for the test archive id.');
        }

        if (file_exists($archiveDirectory))
        {
            throw new RuntimeException('At this point, no archive directory should exist for the test archive id.');
        }
    }

    /**
     * This function tests creating a new document and verifies that a row is
     * added to the `ColbyPages` table.
     *
     * @return void
     */
    public static function newDocumentTest()
    {
        $testArchiveId = '6ec5ee9665576ea4eb94632c9a99baa8faaf6fe8';

        $archiveDirectory = ColbyArchive::absoluteDataDirectoryForArchiveId($testArchiveId);
        $archiveDataFilename = "{$archiveDirectory}/archive.data";

        /**
         * Make sure that no document data exists before we start the tests.
         */

        if (self::documentRowDoesExistForArchiveId($testArchiveId))
        {
            throw new RuntimeException('At this point, no document row should exist for the test archive id.');
        }

        if (file_exists($archiveDirectory))
        {
            throw new RuntimeException('At this point, no archive directory should exist for the test archive id.');
        }

        /**
         * Create a new document and verify that it was created.
         */

        $document = ColbyDocument::documentWithArchiveId($testArchiveId);

        if (!self::documentRowDoesExistForArchiveId($testArchiveId))
        {
            throw new RuntimeException('At this point, a document row should exist for the test archive id.');
        }

        if (!file_exists($archiveDataFilename))
        {
            throw new RuntimeException('At this point, an archive data file should exist for the test archive id.');
        }

        /**
         * Delete the test document.
         */

        self::deleteDocumentAndVerifyForArchiveId($testArchiveId);
    }
}

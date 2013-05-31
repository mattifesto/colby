<?php

ColbyArchiveTests::invalidArchiveIdTest();
ColbyArchiveTests::openAndDeleteTest();

/**
 *
 */
class ColbyArchiveTests
{
    /**
     * @return void
     */
    public static function invalidArchiveIdTest()
    {
        $archiveId = 'abadf00d';

        try
        {
            $archive = ColbyArchive::open($archiveId);
        }
        catch (InvalidArgumentException $e)
        {
            return; /* success */
        }

        throw new RuntimeException('Trying to open a ColbyArchive with an invalid archive id should fail.');
    }

    /**
     * @return void
     */
    public static function openAndDeleteTest()
    {
        $testArchiveId = '2626aac5b3aa6ba8ae35e38067e11dc307c537c2';

        $archiveDataFilename = ColbyArchive::absoluteDataDirectoryForArchiveId($testArchiveId) .
                               '/archive.data';

        /**
         * 2013.05.06
         *
         * TODO: The functionality of ColbyArchive has been expanded. These tests
         * should be improved to test what happens with conflicting URIs and make
         * sure that the `ColbyPages` table rows are created and deleted with the
         * archive.
         */

        // Ensure there isn't an already left over from a previous failed test

        if (is_file($archiveDataFilename))
        {
            ColbyArchive::deleteArchiveWithArchiveId($testArchiveId);

            if (is_file($archiveDataFilename))
            {
                throw new RuntimeException(__FUNCTION__ . ' failed: Unable to clean up test environment.');
            }
        }

        // Test creating and saving an archive

        $archive = ColbyArchive::open($testArchiveId);

        if ($archive->created() !== null)
        {
            throw new RuntimeException(__FUNCTION__ . 'failed: A new unsaved archive should return `null` from the `created` method.');
        }

        $title = 'The Title';
        $subtitle = 'The Subtitle';

        $archive->setStringValueForKey($title, 'title');
        $archive->setStringValueForKey(ColbyConvert::textToHTML($title), 'titleHTML');

        $archive->setStringValueForKey($subtitle, 'subtitle');
        $archive->setStringValueForKey(ColbyConvert::textToHTML($subtitle), 'subtitleHTML');

        if (!$archive->didReserveAndSetURIValue($testArchiveId))
        {
            throw new RuntimeException('The test URI value is not available.');
        }

        /**
         * 2013.05.07
         *
         * At one point, the `didReserveAndSetURIValue` was not setting the 'uri'
         * value on the archive. This test was added to make sure that it does.
         *
         * BUGBUG: This test should be moved.
         */
        if ($archive->valueForKey('uri') !== $testArchiveId)
        {
            throw new RuntimeException('The URI value was not set properly in the archive.');
        }

        $archive->save();

        $archive = null;

        // Test opening an archive

        $archive = ColbyArchive::open($testArchiveId);

        if ($archive->archiveId() !== $testArchiveId)
        {
            throw new RuntimeException(__FUNCTION__ . ' failed: The archive id after opening doesn\'t match the saved archive id.');
        }

        $archive = null;

        // Test deleting an archive

        ColbyArchive::deleteArchiveWithArchiveId($testArchiveId);

        if (is_file($archiveDataFilename))
        {
            throw new RuntimeException(__FUNCTION__ . ' failed: The archive is still exists after deletion.');
        }
    }
}

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

        $archiveDirectory = ColbyArchive::absoluteDataDirectoryForArchiveId($testArchiveId);
        $archiveDataFilename = "{$archiveDirectory}/archive.data";

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

        /**
         * Create a new archive
         */

        $archive = ColbyArchive::open($testArchiveId);

        if ($archive->created() !== null)
        {
            throw new RuntimeException(__FUNCTION__ . 'failed: A new unsaved archive should return `null` from the `created` method.');
        }

        /**
         * Set some values
         */

        $title = 'The & Title';
        $titleHTML = ColbyConvert::textToHTML($title);
        $subtitle = 'The & Subtitle';
        $subtitleHTML = ColbyConvert::textToHTML($subtitle);

        $archive->setStringValueForKey($title, 'title');
        $archive->setStringValueForKey($titleHTML, 'titleHTML');

        $archive->setStringValueForKey($subtitle, 'subtitle');
        $archive->setStringValueForKey($subtitleHTML, 'subtitleHTML');

        /**
         * Confirm values are set
         */

        if ($archive->valueForKey('title') !== $title)
        {
            throw new RuntimeException('The value returned doesn\'t match the value set.');
        }

        if ($archive->valueForKey('titleHTML') !== $titleHTML)
        {
            throw new RuntimeException('The value returned doesn\'t match the value set.');
        }

        if ($archive->valueForKey('subtitle') !== $subtitle)
        {
            throw new RuntimeException('The value returned doesn\'t match the value set.');
        }

        if ($archive->valueForKey('subtitleHTML') !== $subtitleHTML)
        {
            throw new RuntimeException('The value returned doesn\'t match the value set.');
        }

        /**
         * Save the archive
         */

        $archive->save();

        $archive = null;

        /**
         * Re-open the archive
         */

        $archive = ColbyArchive::open($testArchiveId);

        if ($archive->archiveId() !== $testArchiveId)
        {
            throw new RuntimeException(__FUNCTION__ . ' failed: The archive id after opening doesn\'t match the saved archive id.');
        }

        /**
         * Confirm that the values are still set
         */

        if ($archive->valueForKey('title') !== $title)
        {
            throw new RuntimeException('The value returned doesn\'t match the value set.');
        }

        if ($archive->valueForKey('titleHTML') !== $titleHTML)
        {
            throw new RuntimeException('The value returned doesn\'t match the value set.');
        }

        if ($archive->valueForKey('subtitle') !== $subtitle)
        {
            throw new RuntimeException('The value returned doesn\'t match the value set.');
        }

        if ($archive->valueForKey('subtitleHTML') !== $subtitleHTML)
        {
            throw new RuntimeException('The value returned doesn\'t match the value set.');
        }

        $archive = null;

        /**
         * Delete the archive
         */

        ColbyArchive::deleteArchiveWithArchiveId($testArchiveId);

        if (file_exists($archiveDirectory))
        {
            throw new RuntimeException('The archive directory is still exists after deletion.');
        }
    }
}

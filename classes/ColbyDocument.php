<?php

class ColbyDocument
{
    /**
     * The `$archive` variable will hold the ColbyArchive instance associated
     * with this object.
     */
    private $archive = null;

    /**
     * Make the constructor private to force use of the static method
     * constructors.
     */
    private function __construct()
    {
    }

    /**
     * @return ColbyArchive
     */
    public function archive()
    {
        return $this->archive;
    }

    /**
     * This function will delete a document. The function will be successful
     * even if the document does not exist or exists partially.
     *
     * @return void
     */
    public static function deleteDocumentWithArchiveId($archiveId)
    {
        $safeArchiveId = Colby::mysqli()->escape_string($archiveId);

        $sql = "DELETE FROM `ColbyPages` WHERE `archiveId` = UNHEX('{$safeArchiveId}')";

        Colby::query($sql);

        ColbyArchive::deleteArchiveWithArchiveId($archiveId);
    }

    /**
     * @return ColbyDocument
     */
    public static function documentWithArchiveId($archiveId)
    {
        $document = new ColbyDocument();

        $document->archive = ColbyArchive::open($archiveId);

        if (!$document->archive->valueForKey('documentRowId'))
        {
            $safeArchiveId = Colby::mysqli()->escape_string($archiveId);

            /**
             * If the document has already been created it means that this is
             * an older archive that was created before 'documentRowId' was
             * a concept. In this case, we will assume there is a row in
             * the database for the document but that its row id hasn't been
             * saved in the archive.
             *
             * If there is no document row in the database for this archive
             * then this function will throw an exception because it is not
             * appropriate to try to convert an archive to a document in this
             * way.
             *
             * Theoretically this block could go away if all existing document
             * archives were known to have a valid 'documentRowId' value.
             */
            if ($document->archive->created())
            {
                $sql = "SELECT `id` FROM `ColbyPages` WHERE `archiveId` = UNHEX('{$safeArchiveId}')";

                $result = Colby::query($sql);

                $documentRowId = $result->fetch_object()->id;

                $result->free();
            }

            /**
             * If the document hasn't been created yet then add its row to the
             * database using the archive id as the uri.
             */
            else
            {
                $sql = <<<EOT
INSERT INTO `ColbyPages`
(
    `archiveId`,
    `stub`,
    `titleHTML`,
    `subtitleHTML`
)
VALUES
(
    UNHEX('{$safeArchiveId}'),
    '{$safeArchiveId}',
    '',
    ''
)
EOT;

                Colby::query($sql);

                $documentRowId = Colby::mysqli()->insert_id;

                /**
                 * Save the URI in the archive.
                 */

                $document->archive->setStringValueForKey($safeArchiveId, 'uri');
            }

            /**
             * Set the 'documentRowId' value on the archive and then save it.
             */

            $document->archive->setIntValueForKey($documentRowId, 'documentRowId');

            $document->archive->save();
        }

        return $document;
    }

    /**
     * This function updates the database row, except for the URI, and then
     * saves the archive. The reason the URI is not saved is because this
     * function should succeed even if the URI is not available. Use the
     * `setURI` method to update the URI.
     *
     * @return void
     */
    public function save()
    {
        $mysqli = Colby::mysqli();

        $safeDocumentRowId = (int)$this->archive->valueForKey('documentRowId');

        $safeDocumentGroupId = $mysqli->escape_string($this->archive->valueForKey('documentGroupId'));
        $safeDocumentTypeId = $mysqli->escape_string($this->archive->valueForKey('documentTypeId'));

        $safeTitleHTML = $mysqli->escape_string($this->archive->valueForKey('titleHTML'));
        $safeSubtitleHTML = $mysqli->escape_string($this->archive->valueForKey('subtitleHTML'));
        $safeThumbnailURL = $mysqli->escape_string($this->archive->valueForKey('thumbnailURL'));
        $safeSearchText = $mysqli->escape_string($this->archive->valueForKey('searchText'));

        if ($this->archive->valueForKey('isPublished'))
        {
            $safePublished = (int)$this->archive->valueForKey('publishedTimeStamp');
        }
        else
        {
            $safePublished = 'NULL';
        }

        $safePublishedBy = (int)$this->archive->valueForKey('publishedBy');

        if (!$safePublishedBy)
        {
            $safePublishedBy = 'NULL';
        }

        $sql = <<<EOT
UPDATE
    `ColbyPages`
SET
    `groupId` = UNHEX('{$safeDocumentGroupId}'),
    `modelId` = UNHEX('{$safeDocumentTypeId}'),
    `titleHTML` = '{$safeTitleHTML}',
    `subtitleHTML` = '{$safeSubtitleHTML}',
    `thumbnailURL` = '{$safeThumbnailURL}',
    `searchText` = '{$safeSearchText}',
    `published` = {$safePublished},
    `publishedBy` = {$safePublishedBy}
WHERE
    `id` = {$safeDocumentRowId}
EOT;

        Colby::query($sql);

        $this->archive->save();
    }

    /**
     * This function attempts to update the URI value in the document row. This
     * may not succeed if the URI is already used by another document in which
     * case an exception will be thrown.
     *
     * If the URI can be used, the function will also set the 'uri' value on
     * the archive to the value passed in.
     *
     * @return void
     */
    public function setURI($uri)
    {
        $safeDocumentRowId = (int)$this->archive->valueForKey('documentRowId');

        $safeURI = Colby::mysqli()->escape_string($uri);

        $sql = <<<EOT
UPDATE
    `ColbyPages`
SET
    `stub` = '{$safeURI}'
WHERE
    `id` = {$safeDocumentRowId}
EOT;

        Colby::query($sql);

        $this->archive->setStringValueForKey($uri, 'uri');
    }
}

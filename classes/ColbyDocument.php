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
             * a concept. In this case, we will assume there is an row in
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
            if ($document->archive()->created())
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
    (`archiveId`, `stub`, `titleHTML`, `subtitleHTML`)
VALUES
    (UNHEX('{$safeArchiveId}'), '{$safeArchiveId}', '', '')
EOT;

                Colby::query($sql);

                $documentRowId = Colby::mysqli()->insert_id;
            }

            /**
             * Set the 'documentRowId' value on the archive and then save it.
             */

            $document->archive->setIntValueForKey($documentRowId, 'documentRowId');

            $document->archive->save();
        }

        return $document;
    }
}

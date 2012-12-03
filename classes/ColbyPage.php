<?php

class ColbyPage
{
    public $stubBasePart;
    public $stubPagePart; 
    public $stubIsLocked;
    public $stubIsCustom;   

    public $modelId;
    public $groupId;
    
    public $title;
    public $titleHTML;
    public $subtitle;
    public $subtitleHTML;
    
    public $published;
    public $publishedBy;
    public $publicationDate;
    
    /**
     * @return ColbyArchive | bool (false)
     */
    public static function archiveForStub($stub)
    {
        $sqlStub = Colby::mysqli()->escape_string($stub);
        $sqlStub = "'{$sqlStub}'";

        $sql = <<<EOT
SELECT
    LOWER(HEX(`archiveId`)) AS `archiveId`
FROM
    `ColbyPages`
WHERE
    `stub` = {$sqlStub}
EOT;

        $result = Colby::query($sql);

        if ($result->num_rows != 1) // will either be 1 or 0
        {
            return false;
        }

        $archiveId = $result->fetch_object()->archiveId;

        $result->free();

        $archive = ColbyArchive::open($archiveId);

        return $archive;
    }

    /**
     * Deletes a page from the database and deletes the page archive.
     *
     * @return void
     */
    public static function deletePage($archiveId)
    {
        $sqlArchiveId = Colby::mysqli()->escape_string($archiveId);

        $sql = "DELETE FROM `ColbyBlogPosts` WHERE `id` = UNHEX('{$sqlArchiveId}')";

        Colby::query($sql);

        ColbyArchive::delete($archiveId);
    }
    
    /**
     * @return string
     */
    public function suggestedStubWithPrefix($prefix)
    {
    }
    
    /**
     * @return string
     */
    public function stub()
    {
        // TODO: there must be a page part (based on a title)
        //       figure out if temporarily things are allowed to not have a title
        
        if (!empty($this->stubBasePart))
        {
            return "{$this->stubBasePart}/{$this->stubPagePart}";
        }
        else
        {
            return $this->stubPagePart;
        }
    }
    
    /**
     * @return void
     */
    public static function updateDatabaseWithPostArchive(ColbyArchive $archive)
    {
        $sqlId = Colby::mysqli()->escape_string($archive->archiveId());
        $sqlId = "UNHEX('{$sqlId}')";

        $sqlType = Colby::mysqli()->escape_string($archive->rootObject()->type);
        $sqlType = "UNHEX('{$sqlType}')";

        $sqlStub = Colby::mysqli()->escape_string($archive->rootObject()->stub);
        $sqlStub = "'{$sqlStub}'";

        $sqlTitleHTML = Colby::mysqli()->escape_string($archive->rootObject()->titleHTML);
        $sqlTitleHTML = "'{$sqlTitleHTML}'";

        $sqlSubtitleHTML = Colby::mysqli()->escape_string($archive->rootObject()->subtitleHTML);
        $sqlSubtitleHTML = "'{$sqlSubtitleHTML}'";

        $sqlPublished = ColbyConvert::timestampToSQLDateTime($archive->rootObject()->published);

        $sqlPublishedBy = $archive->rootObject()->publishedBy;
        $sqlPublishedBy = empty($sqlPublishedBy) ? 'NULL' : "'{$sqlPublishedBy}'";

        $sql = <<<EOT
INSERT INTO `ColbyBlogPosts`
(
    `id`,
    `type`,
    `stub`,
    `titleHTML`,
    `subtitleHTML`,
    `published`,
    `publishedBy`
)
VALUES
(
    {$sqlId},
    {$sqlType},
    {$sqlStub},
    {$sqlTitleHTML},
    {$sqlSubtitleHTML},
    {$sqlPublished},
    {$sqlPublishedBy}
)
ON DUPLICATE KEY UPDATE
    `stub` = {$sqlStub},
    `titleHTML` = {$sqlTitleHTML},
    `subtitleHTML` = {$sqlSubtitleHTML},
    `published` = {$sqlPublished},
    `publishedBy` = {$sqlPublishedBy}
EOT;

        Colby::query($sql);
    }
}

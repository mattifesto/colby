<?php

class ColbyPage
{
    // All of the declared member variables in this class are public to avoid any unforseen serialization issues. However, these member variables should usually be set with an accessor method rather than directly because some of them are calculated. Obviously, adding any undeclared member variables will require setting them directly, which won't be a problem.

    public $customPageStubText;
    public $pageStub;
    public $preferredPageStub;
    public $stubIsLocked;

    public $modelId;
    public $defaultViewId; // TODO: multiple available views per model?
    public $groupId;
    public $groupStub;

    public $title;
    public $titleHTML;
    public $subtitle;
    public $subtitleHTML;

    public $isPublished;
    public $publishedBy;
    public $publicationDate;

    /**
     * @return ColbyPage
     */
    public function __construct($modelId, $groupId, $groupStub)
    {
        // Set member variables that are constant for the lifetime of the object.

        $this->modelId = $modelId;
        $this->groupId = $groupId;
        $this->groupStub = $groupStub;
    }

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
     * @return void
     */
    public function setPageStubData($preferredPageStub, $pageStubIsLocked)
    {
        // Convert pageStubIsLocked to a Boolean variable and save.

        $this->pageStubIsLocked = !!$pageStubIsLocked;

        // The variable pageStubIsLocked is saved only for use on the client side. The server side (this process) ignores its value. If the client sends an updated preferredPageStub it will be taken as a sign that the page stub is to be re-evaluated. If the preferredPageStub is unchanged it will not be re-evaluated even if it happens that the preferredPageStub had not been available before but has now become available.

        if ($this->preferredPageStub != $preferredPageStub)
        {
            // Setting the pageStub to null indicates that it should be re-evaluated.

            $this->preferredPageStub = $preferredPageStub;
            $this->pageStub = null;
        }
    }

    /**
     * @return void
     */
    public function setPublicationData($isPublished, $publishedBy, $publicationDate)
    {
        $this->isPublished = !!$isPublished;
        $this->publishedBy = $publishedBy ? intval($publishedBy) : null;
        $this->publicationDate = $publicationDate ? intval($publicationDate) : null;
    }

    /**
     * @return string
     */
    public function stub()
    {
        return "{$this->groupStub}/{$this->pageStub}";
    }

    /**
     * @return void
     */
    public function setSubtitle($subtitle)
    {
        $this->subtitle = $subtitle;
        $this->subtitleHTML = ColbyConvert::textToHTML($subtitle);
    }

    /**
     * @return void
     */
    public function setTitle($title)
    {
        $this->title = $title;
        $this->titleHTML = ColbyConvert::textToHTML($title);
    }

    /**
     * @return void
     */
    public function updateDatabaseWithArchiveId($archiveId)
    {
        // TODO: Although it's redundant to duplicate the archive id inside the file itself
        // there have been other places where I've wanted it. One such place is for use as a
        // fallback stub. Way the pros and cons of including the archive id in this class.

        if (!$this->pageStub)
        {
            $preferredStub = "{$this->groupStub}/{$this->preferredPageStub}";

            // TODO: check database to see if its available, for now we just lazily assume

            $this->pageStub = $this->preferredPageStub;
        }

        $sqlArchiveId = Colby::mysqli()->escape_string($archiveId);
        $sqlArchiveId = "UNHEX('{$sqlArchiveId}')";

        if ($this->groupId)
        {
            $sqlGroupId = Colby::mysqli()->escape_string($this->groupId);
            $sqlGroupId = "UNHEX('{$sqlGroupId}')";
        }
        else
        {
            $sqlGroupId = 'NULL';
        }

        $sqlStub = Colby::mysqli()->escape_string($this->stub());
        $sqlStub = "'{$sqlStub}'";

        $sqlTitleHTML = Colby::mysqli()->escape_string($this->titleHTML);
        $sqlTitleHTML = "'{$sqlTitleHTML}'";

        $sqlSubtitleHTML = Colby::mysqli()->escape_string($this->subtitleHTML);
        $sqlSubtitleHTML = "'{$sqlSubtitleHTML}'";

        if ($this->isPublished)
        {
             $sqlPublished = ColbyConvert::timestampToSQLDateTime($this->publicationDate);
        }
        else
        {
            $sqlPublished = 'NULL';
        }

        $sqlPublishedBy = empty($this->publishedBy) ? 'NULL' : "'{$this->publishedBy}'";

        $sql = <<<EOT
INSERT INTO `ColbyPages`
(
    `archiveId`,
    `groupId`,
    `stub`,
    `titleHTML`,
    `subtitleHTML`,
    `published`,
    `publishedBy`
)
VALUES
(
    {$sqlArchiveId},
    {$sqlGroupId},
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

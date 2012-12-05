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
        $this->groupId = $groupId ? $groupId : null;
        $this->groupStub = $groupStub ? $groupStub : null;
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
     * @return void
     */
    public function calculatePageStub()
    {
        $sqlPreferredStub = Colby::mysqli()->escape_string($this->preferredStub());
        $sqlPreferredStub = "'{$sqlPreferredStub}'";

        $sql = "SELECT COUNT(*) as `count` FROM `ColbyPages` WHERE `stub` = {$sqlPreferredStub}";

        $result = Colby::query($sql);

        $count = $result->fetch_object()->count;

        $result->free();

        if ($count > 0)
        {
            // The preferred stub is already in use.

            $this->pageStub = sha1(microtime() . rand());
        }
        else
        {
            $this->pageStub = $this->preferredPageStub;
        }
    }

    /**
     * Deletes a page from the database and deletes the page archive.
     *
     * @return void
     */
    public static function delete($archiveId)
    {
        $sqlArchiveId = Colby::mysqli()->escape_string($archiveId);

        $sql = "DELETE FROM `ColbyPages` WHERE `archiveId` = UNHEX('{$sqlArchiveId}')";

        Colby::query($sql);

        ColbyArchive::delete($archiveId);
    }

    /**
     * @return void
     */
    public function setPageStubData($preferredPageStub, $stubIsLocked)
    {
        // Convert stubIsLocked to a Boolean variable and save.

        $this->stubIsLocked = !!$stubIsLocked;

        // The variable stubIsLocked is saved only for use on the client side. The server side (this process) ignores its value. If the client sends an updated preferredPageStub it will be taken as a sign that the page stub is to be re-evaluated. If the preferredPageStub is unchanged it will not be re-evaluated even if it happens that the preferredPageStub had not been available before but has now become available.

        if ($this->preferredPageStub != $preferredPageStub)
        {
            // Validate the stub. It would be exceptional behavior to pass in an invalid stub which is why we will throw an exeption.

            if (!preg_match('/^[0-9a-zA-Z-]+$/', $preferredPageStub))
            {
                throw new InvalidArgumentException('preferredPageStub');
            }

            $this->preferredPageStub = $preferredPageStub;

            // Setting the pageStub to null indicates that it should be re-evaluated.

            $this->pageStub = null;
        }
    }

    /**
     * @return string
     */
    public function preferredStub()
    {
        return "{$this->groupStub}/{$this->preferredPageStub}";
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
        // fallback stub. Weigh the pros and cons of including the archive id in this class.

        if (!$this->pageStub)
        {
            $this->calculatePageStub();
        }

        $sqlArchiveId = Colby::mysqli()->escape_string($archiveId);
        $sqlArchiveId = "UNHEX('{$sqlArchiveId}')";

        $sqlModelId = Colby::mysqli()->escape_string($this->modelId);
        $sqlModelId = "UNHEX('{$sqlModelId}')";

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
    `modelId`,
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
    {$sqlModelId},
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

<?php

class ColbyPage
{
    // All of the declared member variables in this class are public to avoid any unforseen serialization issues. However, these member variables should usually be set with an accessor method rather than directly because some of them are calculated. Obviously, adding any undeclared member variables will require setting them directly, which won't be a problem.

    public $customPageStubText;
    public $pageStub;
    public $preferredPageStub;
    public $stubIsLocked;

    public $modelId;
    public $viewId;
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
    public static function pageWithViewId($viewId)
    {
        $page = new ColbyPage();

        $viewDataFilename = "handle,admin,view,{$viewId}.data";

        $absoluteViewDataFilename = Colby::findHandler($viewDataFilename);

        $viewData = unserialize(file_get_contents($absoluteViewDataFilename));

        $groupDataFilename = "handle,admin,group,{$viewData->groupId}.data";

        $absoluteGroupDataFilename = Colby::findHandler($groupDataFilename);

        $groupData = unserialize(file_get_contents($absoluteGroupDataFilename));

        // Set member variables that are constant for the lifetime of the object.

        $page->viewId = $viewId;
        $page->modelId = $viewData->modelId;
        $page->groupId = $viewData->groupId;
        $page->groupStub = $groupData->stub;

        return $page;
    }

    /**
     * @return ColbyArchive | bool (false)
     */
    public static function archiveForStub($stub)
    {
        $archive = false;

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
            goto done;
        }

        $archiveId = $result->fetch_object()->archiveId;

        $archive = ColbyArchive::open($archiveId);

        done:

        $result->free();

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
     * A page can't be displayed directly
     * because the full archive information should be made available to the view.
     *
     * @return void
     */
    public static function displayPageForArchiveId($archiveId)
    {
        // TODO: Add something like 'openIfExists' to ColbyArchive
        //       This function is usually only called when we're pretty certain the archive exists
        //       but if it doesn't, we don't want to create it.

        $archive = ColbyArchive::open($archiveId);

        $page = $archive->rootObject();

        $viewFilename = "handle,admin,view,{$page->viewId}.php";

        return include(Colby::findHandler($viewFilename));
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
        if ($this->groupStub)
        {
            return "{$this->groupStub}/{$this->preferredPageStub}";
        }
        else
        {
            return $this->preferredPageStub;
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
        if ($this->groupStub)
        {
            return "{$this->groupStub}/{$this->pageStub}";
        }
        else
        {
            return $this->pageStub;
        }
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

        if ($this->modelId)
        {
            $sqlModelId = Colby::mysqli()->escape_string($this->modelId);
            $sqlModelId = "UNHEX('{$sqlModelId}')";
        }
        else
        {
            $sqlModelId = 'NULL';
        }

        if ($this->viewId)
        {
            $sqlViewId = Colby::mysqli()->escape_string($this->viewId);
            $sqlViewId = "UNHEX('{$sqlViewId}')";
        }
        else
        {
            $sqlViewId = 'NULL';
        }

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
    `viewId`,
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
    {$sqlViewId},
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

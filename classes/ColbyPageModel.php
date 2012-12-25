<?php

class ColbyPageModel
{
    private $archive;
    private $data;

    /**
     * The constructor is private because to create a new ColbyPageModel
     * correctly the static method 'modelWithArchive' should be called.
     *
     * @return ColbyPageModel
     */
    private function __construct()
    {
    }

    /**
     * @return ColbyPageModel
     */
    public static function modelWithArchive(ColbyArchive $archive)
    {
        $model = new ColbyPageModel();

        $model->archive = $archive;
        $model->data = $archive->data();

        return $model;
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
        if (!$this->preferredPageStub())
        {
            $this->data->pageStub = sha1(microtime() . rand());

            return;
        }

        $sqlPreferredStub = Colby::mysqli()->escape_string($this->preferredStub());
        $sqlPreferredStub = "'{$sqlPreferredStub}'";

        $sql = "SELECT COUNT(*) as `count` FROM `ColbyPages` WHERE `stub` = {$sqlPreferredStub}";

        $result = Colby::query($sql);

        $count = $result->fetch_object()->count;

        $result->free();

        if ($count > 0)
        {
            // The preferred stub is already in use.

            $this->data->pageStub = sha1(microtime() . rand());
        }
        else
        {
            $this->data->pageStub = $this->preferredPageStub();
        }
    }

    /**
     * @return string | null
     */
    public function contentSearchText()
    {
        return isset($this->contentSearchText) ? $this->contentSearchText : null;
    }

    /**
     * @return void
     */
    public function setContentSearchText($text)
    {
        // Content search text is not saved to the archive so it is not set on the data object.

        $this->contentSearchText = $text ? strval($text) : null;
    }

    /**
     * @return string | null
     */
    public function customPageStubText()
    {
        return isset($this->data->customPageStubText) ? $this->data->customPageStubText : null;
    }

    /**
     * @return void
     */
    public function setCustomPageStubText($customPageStubText)
    {
        $this->data->customPageStubText = $customPageStubText ? strval($customPageStubText) : null;
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
     * TODO: should this function be moved to the ColbyOutputManager class?
     *
     * @return void
     */
    public static function displayPageForArchiveId($archiveId)
    {
        // TODO: Add something like 'openIfExists' to ColbyArchive
        //       This function is usually only called when we're pretty certain the archive exists
        //       but if it doesn't, we don't want to create it.

        $archive = ColbyArchive::open($archiveId);

        $page = $archive->data();

        $viewFilename = "handle,admin,view,{$page->viewId}.php";

        return include(Colby::findHandler($viewFilename));
    }

    /**
     * @return string | null
     */
    public function groupId()
    {
        return isset($this->data->groupId) ? $this->data->groupId : null;
    }

    /**
     * @return void
     */
    public function setGroupId($groupId)
    {
        $this->data->groupId = $groupId;
    }

    /**
     * @return string | null
     */
    public function groupStub()
    {
        return isset($this->data->groupStub) ? $this->data->groupStub : null;
    }

    /**
     * @return void
     */
    public function setGroupStub($groupStub)
    {
        $this->data->groupStub = $groupStub ? strval($groupStub) : null;
    }

    /**
     * @return string | null
     */
    public function modelId()
    {
        return isset($this->data->modelId) ? $this->data->modelId : null;
    }

    /**
     * @return void
     */
    public function setModelId($modelId)
    {
        $this->data->modelId = $modelId;
    }

    /**
     * @return string | null
     */
    public function pageStub()
    {
        return isset($this->data->pageStub) ? $this->data->pageStub : null;
    }

    /**
     * @return void
     */
    public function setPageStubData($preferredPageStub, $stubIsLocked)
    {
        // Convert stubIsLocked to a Boolean variable and save.

        $this->data->stubIsLocked = !!$stubIsLocked;

        // The variable stubIsLocked is saved only for use on the client side. The server side (this process) ignores its value. If the client sends an updated preferredPageStub it will be taken as a sign that the page stub is to be re-evaluated. If the preferredPageStub is unchanged it will not be re-evaluated even if it happens that the preferredPageStub had not been available before but has now become available.

        if ($this->preferredPageStub() != $preferredPageStub)
        {
            // Validate the stub. It would be exceptional behavior to pass in an invalid stub which is why we will throw an exeption.

            if (!preg_match('/^[0-9a-zA-Z-]+$/', $preferredPageStub))
            {
                throw new InvalidArgumentException('preferredPageStub');
            }

            $this->data->preferredPageStub = $preferredPageStub;

            // Setting the pageStub to null indicates that it should be re-evaluated.

            $this->data->pageStub = null;
        }
    }

    /**
     * @return string
     */
    public function preferredPageStub()
    {
        return isset($this->data->preferredPageStub) ? $this->data->preferredPageStub : null;
    }

    /**
     * @return void
     */
    public function setPreferredPageStub($preferredPageStub)
    {
        $this->data->preferredPageStub = $preferredPageStub ? strval($preferredPageStub) : null;
    }

    /**
     * @return string
     */
    public function preferredStub()
    {
        $groupStub = $this->groupStub();
        $preferredPageStub = $this->preferredPageStub();

        if ($groupStub)
        {
            return "{$groupStub}/{$preferredPageStub}";
        }
        else
        {
            return $preferredPageStub;
        }
    }

    /**
     * @return void
     */
    public function setPublicationData($isPublished, $publishedBy, $publicationDate)
    {
        $this->data->isPublished = !!$isPublished;
        $this->data->publishedBy = $publishedBy ? intval($publishedBy) : null;
        $this->data->publicationDate = $publicationDate ? intval($publicationDate) : null;
    }

    /**
     * @return int | null
     */
    public function publicationDate()
    {
        return isset($this->data->publicationDate) ? $this->data->publicationDate : null;
    }

    /**
     * @return void
     */
    public function setPublicationDate($publicationDate)
    {
        $this->data->publicationDate = $publicationDate ? intval($publicationDate) : null;
    }

    /**
     * @return bool
     */
    public function isPublished()
    {
        return isset($this->data->isPublished) ? $this->data->isPublished : null;
    }

    /**
     * @return void
     */
    public function setIsPublished($isPublished)
    {
        $this->data->isPublished = !!$isPublished;
    }

    /**
     * @return int | null
     */
    public function publishedBy()
    {
        return isset($this->data->publishedBy) ? $this->data->publishedBy : null;
    }

    /**
     * @return void
     */
    public function setPublishedBy($publishedBy)
    {
        $this->data->publishedBy = $publishedBy ? intval($publishedBy) : null;
    }

    /**
     * @return string
     */
    private function searchText()
    {
        // Discussion
        //
        // Dates: I'm not including dates right now in search terms. It might
        // be a nice feature to include the work 'August' in items with that
        // were published in August but what if the date is irrelevant for
        // searches, as it is with pages that don't display a published date.
        // There may be certain specific models that need to include dates
        // but generically it doesn't seem like a good idea.
        //
        // Published By: This seems more likely to be desireable except
        // again in the case of pages where the person who published it is
        // not display to the user. I'm leaving this out for now but blog
        // posts may wish to include it. It may be the type of thing that should
        // always be included only if the specific model deems it necessary.

        $searchableData = array();

        // In order of statistically most likely to match any given search query

        $searchableData[] = $this->archive->valueForKey('title');
        $searchableData[] = $this->archive->valueForKey('subtitle');
        $searchableData[] = $this->contentSearchText();
        $searchableData[] = $this->stub();
        $searchableData[] = $this->data->archiveId;
        $searchableData[] = $this->groupId();
        $searchableData[] = $this->modelId();
        $searchableData[] = $this->viewId();

        return implode(' ', $searchableData);
    }

    /**
     * @return string
     */
    public function stub()
    {
        $groupStub = $this->groupStub();
        $pageStub = $this->pageStub();

        if ($groupStub)
        {
            return "{$groupStub}/{$pageStub}";
        }
        else
        {
            return $pageStub;
        }
    }

    /**
     * @return bool
     */
    public function stubIsLocked()
    {
        return isset($this->data->stubIsLocked) ? $this->data->stubIsLocked : null;
    }

    /**
     * @return void
     */
    public function setStubIsLocked($stubIsLocked)
    {
        $this->data->stubIsLocked = !!$stubIsLocked;
    }

    /**
     * @return void
     */
    public function updateDatabase()
    {
        // TODO: Although it's redundant to duplicate the archive id inside the file itself
        // there have been other places where I've wanted it. One such place is for use as a
        // fallback stub. Weigh the pros and cons of including the archive id in this class.

        if (!isset($this->data->pageStub))
        {
            $this->calculatePageStub();
        }

        $sqlArchiveId = Colby::mysqli()->escape_string($this->data->archiveId);
        $sqlArchiveId = "UNHEX('{$sqlArchiveId}')";

        if ($this->modelId())
        {
            $sqlModelId = Colby::mysqli()->escape_string($this->modelId());
            $sqlModelId = "UNHEX('{$sqlModelId}')";
        }
        else
        {
            $sqlModelId = 'NULL';
        }

        if ($this->viewId())
        {
            $sqlViewId = Colby::mysqli()->escape_string($this->viewId());
            $sqlViewId = "UNHEX('{$sqlViewId}')";
        }
        else
        {
            $sqlViewId = 'NULL';
        }

        if ($this->groupId())
        {
            $sqlGroupId = Colby::mysqli()->escape_string($this->groupId());
            $sqlGroupId = "UNHEX('{$sqlGroupId}')";
        }
        else
        {
            $sqlGroupId = 'NULL';
        }

        $sqlStub = Colby::mysqli()->escape_string($this->stub());
        $sqlStub = "'{$sqlStub}'";

        $sqlTitleHTML = Colby::mysqli()->escape_string($this->archive->valueForKey('titleHTML'));
        $sqlTitleHTML = "'{$sqlTitleHTML}'";

        $sqlSubtitleHTML = Colby::mysqli()->escape_string($this->archive->valueForKey('subtitleHTML'));
        $sqlSubtitleHTML = "'{$sqlSubtitleHTML}'";

        $sqlSearchText = Colby::mysqli()->escape_string($this->searchText());
        $sqlSearchText = "'{$sqlSearchText}'";

        if ($this->isPublished())
        {
             $sqlPublished = ColbyConvert::timestampToSQLDateTime($this->publicationDate());
        }
        else
        {
            $sqlPublished = 'NULL';
        }

        if ($this->publishedBy())
        {
            $sqlPublishedBy = Colby::mysqli()->escape_string($this->publishedBy());
            $sqlPublishedBy = "'{$sqlPublishedBy}'";
        }
        else
        {
            $sqlPublishedBy = 'NULL';
        }

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
    `searchText`,
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
    {$sqlSearchText},
    {$sqlPublished},
    {$sqlPublishedBy}
)
ON DUPLICATE KEY UPDATE
    `stub` = {$sqlStub},
    `titleHTML` = {$sqlTitleHTML},
    `subtitleHTML` = {$sqlSubtitleHTML},
    `searchText` = {$sqlSearchText},
    `published` = {$sqlPublished},
    `publishedBy` = {$sqlPublishedBy}
EOT;

        Colby::query($sql);
    }

    /**
     * @return string | null
     */
    public function viewId()
    {
        return isset($this->data->viewId) ? $this->data->viewId : null;
    }

    /**
     * @return void
     */
    public function setViewId($viewId)
    {
        $viewDataFilename = "handle,admin,view,{$viewId}.data";

        $absoluteViewDataFilename = Colby::findHandler($viewDataFilename);

        $viewData = unserialize(file_get_contents($absoluteViewDataFilename));

        $groupDataFilename = "handle,admin,group,{$viewData->groupId}.data";

        $absoluteGroupDataFilename = Colby::findHandler($groupDataFilename);

        $groupData = unserialize(file_get_contents($absoluteGroupDataFilename));

        // Set member variables that are constant for the lifetime of the object.

        $this->data->viewId = $viewId;
        $this->data->modelId = $viewData->modelId;
        $this->data->groupId = $viewData->groupId;
        $this->data->groupStub = $groupData->stub;
    }
}

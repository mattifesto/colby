<?php

class ColbyBlog
{
    /**
     * @return ColbyArchive | bool (false)
     */
    public static function archiveForStub($stub)
    {
        $sqlStub = Colby::mysqli()->escape_string($stub);
        $sqlStub = "'{$sqlStub}'";

        $sql = <<<EOT
SELECT
    LOWER(HEX(`id`)) AS `id`
FROM
    `ColbyBlogPosts`
WHERE
    `stub` = {$sqlStub}
EOT;

        $result = Colby::query($sql);

        if ($result->num_rows != 1) // will either be 1 or 0
        {
            return false;
        }

        $archiveId = $result->fetch_object()->id;

        $result->free();

        $archive = ColbyArchive::open($archiveId);

        return $archive;
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

    /**
     * Deletes a post from the database and deletes the post archive.
     *
     * @return void
     */
    public static function deletePost($id)
    {
        $sqlId = Colby::mysqli()->escape_string($id);

        $sql = "DELETE FROM `ColbyBlogPosts` WHERE `id` = UNHEX('{$sqlId}')";

        Colby::query($sql);

        ColbyArchive::delete($id);
    }
}
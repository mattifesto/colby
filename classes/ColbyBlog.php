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

        if ($result->num_rows != 1)
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
    public static function update(ColbyArchive $archive)
    {
        $sqlId = Colby::mysqli()->escape_string($archive->archiveId());
        $sqlId = "UNHEX('{$sqlId}')";

        $sqlType = Colby::mysqli()->escape_string($archive->rootObject()->type);
        $sqlType = "UNHEX('{$sqlType}')";

        $sqlStub = Colby::mysqli()->escape_string($archive->rootObject()->stub);
        $sqlStub = "'{$sqlStub}'";

        $sqlPublished = ColbyConvert::timestampToSQLDateTime($archive->rootObject()->published);

        $sql = <<<EOT
INSERT INTO `ColbyBlogPosts`
(
    `id`,
    `type`,
    `stub`,
    `published`
)
VALUES
(
    {$sqlId},
    {$sqlType},
    {$sqlStub},
    {$sqlPublished}
)
ON DUPLICATE KEY UPDATE
    `stub` = {$sqlStub},
    `published` = {$sqlPublished}
EOT;

        Colby::query($sql);
    }

    /**
     * @return void
     */
    public static function delete($id)
    {
    }
}
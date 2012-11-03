<?php

class ColbyBlog
{
    /**
     * @return void
     */
    public static function update(ColbyArchive $archive)
    {
        $sqlId = Colby::mysqli()->escape_string($archive->archiveId());
        $sqlId = "UNHEX('{$sqlId}')";

        $sqlStub = Colby::mysqli()->escape_string($archive->rootObject()->stub);
        $sqlStub = "'{$sqlStub}'";

        $sqlPublished = ColbyConvert::timestampToSQLDateTime($archive->rootObject()->published);

        $sql = <<<EOT
INSERT INTO `ColbyBlogPosts`
(
    `id`,
    `stub`,
    `published`
)
VALUES
(
    {$sqlId},
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
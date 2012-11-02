<?php

class ColbyBlog
{
    /**
     * @return void
     */
    public static function update($id, $stub, $published)
    {
        // NOTE: caller is responsible for passing a valid id
        //       a 40 character hexadecimal number

        $sqlId = Colby::mysqli()->escape_string($id);
        $sqlId = "UNHEX('{$sqlId}')";

        $sqlStub = Colby::mysqli()->escape_string($stub);
        $sqlStub = "'{$sqlStub}'";

        $sqlPublished = ColbyConvert::timestampToSQLDateTime($published);

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
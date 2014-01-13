<?php

/**
 * 2014.01.11
 *
 * This is a big update. It updates many of the columns of the ColbyPages
 * table to make more sense and take into account lessons learned over the
 * past years.
 *
 * -------------------------------------
 *
 * `modelId` is being renamed to `typeId`
 * `viewId` is being removed
 *
 * History
 * -------
 *
 * When these columns were originally created, the idea was to follow a
 * model-view-controller paradigm. A page would have a model, with a specific
 * data schema, and then that model could be displayed with different views.
 *
 * Models were specific to groups, meaning one model could not be used for both
 * "blog posts" and "press releases". This bad decision had something to do
 * with needing different editors for different groups, but I don't remember
 * exactly.
 *
 * A system needs to be more flexible to support the ever changing needs of a
 * website. It's not uncommon to realize that a new piece of data is needed for
 * a page, but with this paradigm any new data meant that the model had changed
 * and that every view had to be updated to support the model change. Every
 * model had its own editor which would also need to be updated. To keep track
 * of everything, a tool was created to create and manage groups, models, and
 * views. To reduce the unmanageable complexity, views were dropped as an idea
 * (although the `viewId` column was not removed). The remaining high level of
 * complexity of the overall system made creating new designs for web pages
 * painful and practically impossible.
 *
 * The goal of these changes is to allow a developer to very quickly create
 * pages with new and distinct designs without disturbing other existing pages.
 * Many additional changes outside of the database are being made toward that
 * goal.
 *
 * Changes
 * -------
 *
 * The `modelId` column is being renamed to `typeId` and no longer has any
 * relationship to the `groupId` column.  Changes to the handling of `typeId`
 * in the `ColbyRequest` class will allow for much simpler page handler
 * management.
 *
 * The `viewId` column is being removed to reduce confusion since it isn't
 * even used.
 *
 * -------------------------------------
 *
 * `titleHTML` and `subtitleHTML`
 *  are changing from
 * `VARCHAR(150) NOT NULL` to `TEXT NOT NULL`
 *
 * History
 * -------
 *
 * The idea was that at least title should be short enough to always fit inside
 * a twitter post. However, that is probably not universally desired and even
 * if it was it should not and can not properly be enforced by the database
 * column length.
 *
 * I don't know why subtitle was given the same limitation.
 *
 * Changes
 * -------
 *
 * The `TEXT` type is the correct type here. It's flexible and it's left to the
 * implementation to control the size of the content rather than the database.
 *
 * -------------------------------------
 *
 * `stub` is being renamed to `URI`
 *
 * History
 * -------
 *
 * In the early days of Colby there was an idea that each URI would be composed
 * of a limited number of stubs with meanings assigned to the stubs at different
 * positions for all pages. For example, the URI "blog-posts/my-favorite-things"
 * contains two stubs "blog-posts" and "my-favorite-things". The first stub
 * might always be the category. It was proposed that having a restricted URI
 * structure would aid in simplicity.
 *
 * The idea fell by the wayside, but the word "stub" unfortunately stuck around
 * as the name for the column that actually represented the URI for a given
 * page. So now, the column is being renamed to `URI`.
 *
 * -------------------------------------
 *
 * `id` is being renamed to `ID`
 * `archiveId` is being renamed to `archiveID`
 * `groupId` is being renamed to `groupID`
 *
 * Column names in MySQL are not case sensitive, however they do retain their
 * given case for display. In most ways renaming these columns does not have
 * any effect, however when describing the table or any other command that
 * displays the column names they will be displayed with the new case.
 *
 * During the course of investigating the requirements for this upgrade it was
 * proven that "ID" was more appropriate than "id" or "Id". The primary reason
 * is that one would never write ID in lowercase in prose because it would be
 * confused with the psychological term "id".
 */

ColbyUpgrade005200::upgrade();

return;

/**
 *
 */
class ColbyUpgrade005200
{
    /**
     * @return void
     */
    public static function modifyTheSubtitleHTMLColumn()
    {
        $sql = <<<EOT

        ALTER TABLE
            `ColbyPages`
        MODIFY
            `subtitleHTML` TEXT NOT NULL

EOT;

        Colby::query($sql);
    }

    /**
     * @return void
     */
    public static function modifyTheTitleHTMLColumn()
    {
        $sql = <<<EOT

        ALTER TABLE
            `ColbyPages`
        MODIFY
            `titleHTML` TEXT NOT NULL

EOT;

        Colby::query($sql);
    }

    /**
     * @return void
     */
    public static function removeTheViewIdColumn()
    {
        $sql = <<<EOT

        ALTER TABLE
            `ColbyPages`
        DROP
            `viewId`

EOT;

        Colby::query($sql);
    }

    /**
     * @return void
     */
    public static function renameTheArchiveIdColumn()
    {
        $sql = <<<EOT

        ALTER TABLE
            `ColbyPages`
        CHANGE
            `archiveId` `archiveID` BINARY(20) NOT NULL

EOT;

        Colby::query($sql);
    }

    /**
     * @return void
     */
    public static function renameTheGroupIdColumn()
    {
        $sql = <<<EOT

        ALTER TABLE
            `ColbyPages`
        CHANGE
            `groupId` `groupID` BINARY(20)

EOT;

        Colby::query($sql);
    }

    /**
     * @return void
     */
    public static function renameTheIdColumn()
    {
        $sql = <<<EOT

        ALTER TABLE
            `ColbyPages`
        CHANGE
            `id` `ID` BIGINT UNSIGNED NOT NULL AUTO_INCREMENT

EOT;

        Colby::query($sql);
    }

    /**
     * @return void
     */
    public static function renameTheModelIdColumn()
    {
        $sql = <<<EOT

        ALTER TABLE
            `ColbyPages`
        CHANGE
            `modelId` `typeID` BINARY(20)

EOT;

        Colby::query($sql);
    }

    /**
     * @return void
     */
    public static function renameTheStubColumn()
    {
        $sql = <<<EOT

        ALTER TABLE
            `ColbyPages`
        CHANGE
            `stub` `URI` VARCHAR(100)

EOT;

        Colby::query($sql);
    }

    /**
     * @return void
     */
    public static function upgrade()
    {
        if (!self::upgradeIsNeeded())
        {
            return;
        }

        self::modifyTheTitleHTMLColumn();
        self::modifyTheSubtitleHTMLColumn();
        self::removeTheViewIdColumn();
        self::renameTheArchiveIdColumn();
        self::renameTheGroupIdColumn();
        self::renameTheIdColumn();
        self::renameTheModelIdColumn();
        self::renameTheStubColumn();
    }

    /**
     * @return bool
     */
    public static function upgradeIsNeeded()
    {
        $sql = <<<EOT

        SELECT
            COUNT(*) as `tableHasModelIdColumn`
        FROM
            information_schema.COLUMNS
        WHERE
            TABLE_SCHEMA = DATABASE() AND
            TABLE_NAME = 'ColbyPages' AND
            COLUMN_NAME = 'modelId'

EOT;

        $result = Colby::query($sql);

        $tableHasModelIdColumn = $result->fetch_object()->tableHasModelIdColumn;

        $result->free();

        return $tableHasModelIdColumn;
    }
}

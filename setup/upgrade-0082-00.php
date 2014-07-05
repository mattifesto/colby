<?php

/**
 * 2014.07.03
 *
 */

ColbyUpgrade008200::upgrade();

return;

/**
 *
 */
class ColbyUpgrade008200
{
    /**
     * @return void
     */
    public static function addTheClassNameColumnToCBPagesInTheTrash()
    {
        $SQL = <<<EOT

            ALTER TABLE
                `CBPagesInTheTrash`
            ADD
                `className` VARCHAR(80)
            AFTER
                `keyValueData`

EOT;

        Colby::query($SQL);
    }

    /**
     * @return void
     */
    public static function addTheClassNameColumnToColbyPages()
    {
        $SQL = <<<EOT

            ALTER TABLE
                `ColbyPages`
            ADD
                `className` VARCHAR(80)
            AFTER
                `keyValueData`

EOT;

        Colby::query($SQL);
    }

    /**
     * @return void
     */
    public static function upgrade()
    {
        if (self::upgradeIsNeeded())
        {
            self::addTheClassNameColumnToColbyPages();
            self::addTheClassNameColumnToCBPagesInTheTrash();
        }
    }

    /**
     * @return void
     */
    public static function upgradeIsNeeded()
    {
        $sql = <<<EOT

            SELECT
                COUNT(*) as `colbyPagesHasAClassNameColumn`
            FROM
                information_schema.COLUMNS
            WHERE
                TABLE_SCHEMA    = DATABASE() AND
                TABLE_NAME      = 'ColbyPages' AND
                COLUMN_NAME     = 'className'

EOT;

        $result = Colby::query($sql);

        $colbyPagesHasAClassNameColumn = $result->fetch_object()->colbyPagesHasAClassNameColumn;

        $result->free();

        return !$colbyPagesHasAClassNameColumn;
    }
}

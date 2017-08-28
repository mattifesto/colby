<?php

/**
 * 2016.03.15
 *
 * Saving pages was setting `classNameForKind` to an empty string instead of
 * NULL. This fixes affected records and the save process has been updated to
 * prevent it in the future.
 */
final class CBUpgradesForVersion188 {

    /**
     * @return null
     */
    static function run() {
        $SQL = <<<EOT

            UPDATE `ColbyPages`
            SET `classNameForKind` = NULL
            WHERE `classNameForKind` = '';

EOT;

        Colby::query($SQL);
    }
}

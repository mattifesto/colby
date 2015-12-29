<?php

/**
 * Converts CBThemedTextViewTheme and CBThemedMenuViewTheme to CBTheme
 */
final class CBUpgradesForVersion178 {

    /**
     * @return null
     */
    public static function run() {

        // CBThemedTextViewTheme --> CBTheme

        $SQL = <<<EOT

            SELECT      `v`.`specAsJSON`
            FROM        `CBModels` AS `m`
            JOIN        `CBModelVersions` AS `v` ON `m`.`ID` = `v`.`ID` AND `m`.`version` = `v`.`version`
            WHERE       `m`.`className` = 'CBThemedTextViewTheme'

EOT;

        $specs = CBDB::SQLToArray($SQL, ['valueIsJSON' => true]);

        array_walk($specs, function ($spec) {
            $spec->className = 'CBTheme';
            $spec->classNameForKind = 'CBTextView';
        });

        CBModels::save($specs);

        // CBThemedMenuViewTheme --> CBTheme

        $SQL = <<<EOT

            SELECT      `v`.`specAsJSON`
            FROM        `CBModels` AS `m`
            JOIN        `CBModelVersions` AS `v` ON `m`.`ID` = `v`.`ID` AND `m`.`version` = `v`.`version`
            WHERE       `m`.`className` = 'CBThemedMenuViewTheme'

EOT;

        $specs = CBDB::SQLToArray($SQL, ['valueIsJSON' => true]);

        array_walk($specs, function ($spec) {
            $spec->className = 'CBTheme';
            $spec->classNameForKind = 'CBMenuView';
        });

        CBModels::save($specs);
    }
}

<?php

/**
 * 2015.12.28
 *
 * Converts CBThemedTextViewTheme and CBThemedMenuViewTheme to CBTheme
 */
final class CBUpgradesForVersion178 {

    /**
     * @return void
     */
     static function CBInstall_install(): void {

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

    /**
     * @return [string]
     */
    static function CBInstall_requiredClassNames(): array {
        return ['CBModels'];
    }
}

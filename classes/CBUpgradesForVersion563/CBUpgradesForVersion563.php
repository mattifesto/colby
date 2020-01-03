<?php

final class CBUpgradesForVersion563 {

    /* -- CBInstall interfaces -- -- -- -- -- */



    /**
     * @return void
     */
    static function CBInstall_install(): void {
        $emailColumnExists = CBDBA::tableHasColumnNamed(
            'ColbyUsers',
            'email'
        );

        if ($emailColumnExists) {
            return;
        }

        $SQL = <<<EOT

            ALTER TABLE     ColbyUsers

            ADD COLUMN      email VARCHAR(254)
                AFTER       hash,

            ADD UNIQUE KEY  email (
                email
            ),

            MODIFY COLUMN   facebookId BIGINT UNSIGNED

        EOT;

        Colby::query($SQL);
    }
    /* CBInstall_install() */



    /**
     * @return [string]
     */
    static function CBInstall_requiredClassNames(): array {
        return [
            'CBUpgradesForVersion546',
            'CBUsers',
        ];
    }

}

<?php

final class CBInstall {

    /**
     * In theory all databases created should have these setting by default, but
     * most likely they will not because hosted MySQL servers have different
     * defaults for various reasons.
     */
    static function install() {
        $SQL = <<<EOT

            ALTER DATABASE
            DEFAULT CHARSET=utf8mb4
            COLLATE=utf8mb4_unicode_520_ci

EOT;

        Colby::query($SQL);
    }
}

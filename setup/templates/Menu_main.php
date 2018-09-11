<?php

/**
 * @TODO 2018_08_20
 *
 *      File should be renamed to Menu_main.php
 *      Class should be renamed to PREFIXMenu_main
 */
final class PREFIXMenu_main {

    /**
     * @return void
     */
    static function CBInstall_install(): void {
        $updater = CBModelUpdater::fetch(
            (object)[
                'className' => 'CBMenu',
                'ID' => PREFIXMenu_main::ID(),
                'title' => 'Website',
                'titleURI' => '/',
            ]
        );

        CBModelUpdater::save($updater);
    }

    /**
     * @return [string]
     */
    static function CBInstall_requiredClassNames(): array {
        return [
            'CBMenu',
            'CBModelUpdater',
        ];
    }

    /**
     * @return ID
     */
    static function ID(): string {
        return RANDOMID;
    }
}

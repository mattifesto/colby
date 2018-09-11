<?php

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

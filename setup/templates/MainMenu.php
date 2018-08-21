<?php

/**
 * @TODO 2018_08_20
 *
 *      File should be renamed to Menu_main.php
 *      Class should be renamed to PREFIXMenu_main
 *      Implementation should use CBModelUpdater
 *      Implementation should use CBMenu::addOrReplaceItem()
 */
final class PREFIXMainMenu {

    /**
     * @return void
     */
    static function CBInstall_install(): void {
        $originalSpec = CBModels::fetchSpecByID(PREFIXMainMenu::ID());

        if (empty($originalSpec)) {
            $spec = (object)[
                'ID' => PREFIXMainMenu::ID(),
                'title' => 'Website',
                'titleURI' => '/',
                'items' => [
                    (object)[
                        'className' => 'CBMenuItem',
                        'name' => 'blog',
                        'text' => 'Blog',
                        'URL' => '/blog/',
                    ],
                ],
            ];
        } else {
            $spec = CBModel::clone($originalSpec);
        }

        $spec->className = 'CBMenu';

        /* save if modified */

        if ($spec != $originalSpec) {
            CBDB::transaction(function () use ($spec) {
                CBModels::save($spec);
            });
        }
    }

    /**
     * @return [string]
     */
    static function CBInstall_requiredClassNames(): array {
        return [
            'CBModels'
        ];
    }

    /**
     * @return ID
     */
    static function ID(): string {
        return RANDOMID;
    }
}

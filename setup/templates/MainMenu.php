<?php

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
        return ['CBModels'];
    }

    /**
     * @return ID
     */
    static function ID(): string {
        return RANDOMID;
    }
}

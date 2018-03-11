<?php

final class CBWellKnownMenuForMain {

    /**
     * @return hex160
     */
    static function ID() {
        return 'fa0a9625d16acb42a5f6fc94ff40b7e48658936b';
    }

    /**
     * @return void
     */
    static function CBInstall_install(): void {
        $originalSpec = CBModels::fetchSpecByID(CBWellKnownMenuForMain::ID());

        if ($originalSpec === false) {
            $spec = (object)[
                'ID' => CBWellKnownMenuForMain::ID(),
                'title' => 'Website',
                'titleURI' => '/',
            ];
        } else {
            $spec = CBModel::clone($originalSpec);
        }

        $spec->className = 'CBMenu';

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
        return ['CBModels', 'CBMenu'];
    }
}

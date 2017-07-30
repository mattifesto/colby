<?php

final class CBWellKnownMenuForMain {

    /**
     * @return hex160
     */
    static function ID() {
        return 'fa0a9625d16acb42a5f6fc94ff40b7e48658936b';
    }

    /**
     * @return null
     */
    static function install() {
        $originalSpec = CBModels::fetchSpecByID(CBWellKnownMenuForMain::ID());

        if ($originalSpec === false) {
            $spec = (object)[
                'ID' => CBWellKnownMenuForMain::ID(),
                'title' => 'Main Menu',
            ];
        } else {
            $spec = clone $originalSpec;
        }

        $spec->className = 'CBMenu';

        if ($spec != $originalSpec) {
            CBDB::transaction(function () use ($spec) {
                CBModels::save([$spec]);
            });
        }
    }
}

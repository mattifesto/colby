<?php

final class CBWellKnownPageForTestingCBTextView2 {

    const ID = 'ab9a674ce554ce49b7a1d1415f219f8fcf8a3e1f';

    /**
     * This page can be used as a sandbox. When the site is updated, this page
     * will be resaved if it has either been changed by a user or if the spec
     * defined here has been updated.
     *
     * @return null
     */
    static function install() {
        $spec = (object)[
            'ID' => CBWellKnownPageForTestingCBTextView2::ID,
            'className' => 'CBViewPage',
            'description' => 'A page for testing and experimenting with CBTextView2.',
            'layout' => (object)[
                'className' => 'CBPageLayout',
                'CSSClassNames' => 'CBLightTheme',
            ],
            'title' => 'Well-Known Page for Testing CBTextView2',
        ];

        $savedSpec = CBModels::fetchSpecByID(CBWellKnownPageForTestingCBTextView2::ID);

        if (!empty($savedSpec->version)) {
            $spec->version = $savedSpec->version;
        }

        include __DIR__ . '/sections.php';


        if ($spec != $savedSpec) {
            CBModels::save([$spec]);
        }
    }
}

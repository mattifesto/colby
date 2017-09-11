<?php

final class CBWellKnownPageForTestingPageTitleAndBodyText {

    const ID = 'f42f10168d15e025bb99231f034b5bf319da7388';

    /**
     * @return null
     */
    static function install() {
        $spec = (object)[
            'ID' => CBWellKnownPageForTestingPageTitleAndBodyText::ID,
            'className' => 'CBViewPage',
            'description' => 'A page for testing and experimenting with CBThemedTextView.',
            'layout' => (object)[
                'className' => 'CBPageLayout',
                'CSSClassNames' => 'CBLightTheme',
            ],
            'title' => 'Well-Known Page for Testing CBThemedTextView',
        ];

        $savedSpec = CBModels::fetchSpecByID(CBWellKnownPageForTestingPageTitleAndBodyText::ID);

        if (!empty($savedSpec->version)) {
            $spec->version = $savedSpec->version;
        }

        include __DIR__ . '/sections.php';

        if ($spec != $savedSpec) {
            CBDB::transaction(function () use ($spec) {
                CBModels::save([$spec]);
            });
        }
    }
}

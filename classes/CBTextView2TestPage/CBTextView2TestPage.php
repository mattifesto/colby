<?php

final class CBTextView2TestPage {

    /**
     * This page can be used as a sandbox. When the site is updated, this page
     * will be resaved if it has either been changed by a user or if the spec
     * defined here has been updated.
     *
     * @return void
     */
    static function CBInstall_install(): void {
        $originalSpec = CBModels::fetchSpecByID(CBTextView2TestPage::ID());

        if (empty($originalSpec)) {
            $spec = (object)[
                'ID' => CBTextView2TestPage::ID(),
            ];
        } else {
            $spec = CBModel::clone($originalSpec);
        }

        $properties = (object)[
            'className' => 'CBViewPage',
            'description' => 'A page for testing and experimenting with CBTextView2.',
            'layout' => (object)[
                'className' => 'CBPageLayout',
                'CSSClassNames' => 'CBLightTheme',
            ],
            'title' => 'CBTextView2 Test Page',
        ];

        CBModel::merge($spec, $properties);

        include __DIR__ . '/sections.php';

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
        return ['CBModels', 'CBPages', 'CBViewPage'];
    }

    /**
     * @return hex160
     */
    static function ID(): string {
        return 'ab9a674ce554ce49b7a1d1415f219f8fcf8a3e1f';
    }
}

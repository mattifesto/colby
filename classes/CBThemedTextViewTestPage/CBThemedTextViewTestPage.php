<?php

final class CBThemedTextViewTestPage {

    /**
     * @return void
     */
    static function CBInstall_install(): void {
        $originalSpec = CBModels::fetchSpecByID(CBThemedTextViewTestPage::ID());

        if (empty($originalSpec)) {
            $spec = (object)[
                'ID' => CBThemedTextViewTestPage::ID(),
            ];
        } else {
            $spec = CBModel::clone($originalSpec);
        }

        $properties = (object)[
            'className' => 'CBViewPage',
            'description' => 'A page for testing and experimenting with CBThemedTextView.',
            'layout' => (object)[
                'className' => 'CBPageLayout',
                'CSSClassNames' => 'CBLightTheme',
            ],
            'title' => 'CBThemedTextView Test Page',
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
        return 'f42f10168d15e025bb99231f034b5bf319da7388';
    }
}

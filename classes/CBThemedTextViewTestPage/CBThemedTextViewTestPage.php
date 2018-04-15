<?php

final class CBThemedTextViewTestPage {

    /**
     * @return void
     */
    static function CBInstall_configure(): void {
        CBModels::deleteByID(CBThemedTextViewTestPage::ID());

        $spec = CBModelTemplateCatalog::fetchLivePageTemplate();

        CBModel::merge($spec, (object)[
            'ID' => CBThemedTextViewTestPage::ID(),
            'description' => 'A page for testing and experimenting with CBThemedTextView.',
            'title' => 'CBThemedTextView Test Page',
        ]);

        include __DIR__ . '/sections.php';

        CBDB::transaction(function () use ($spec) {
            CBModels::save($spec);
        });
    }

    /**
     * @return hex160
     */
    static function ID(): string {
        return 'f42f10168d15e025bb99231f034b5bf319da7388';
    }
}

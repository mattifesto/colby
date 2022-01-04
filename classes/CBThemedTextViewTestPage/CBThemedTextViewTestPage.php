<?php

final class
CBThemedTextViewTestPage {

    /* -- CBInstall interfaces -- */



    /**
     * @return void
     */
    static function
    CBInstall_configure(
    ): void {
        CBDB::transaction(
            function () {
                CBModels::deleteByID(
                    CBThemedTextViewTestPage::ID()
                );
            }
        );

        $spec = CBModelTemplateCatalog::fetchLivePageTemplate();

        CBModel::merge(
            $spec,
            (object)[
                'ID' => CBThemedTextViewTestPage::ID(),
                'description' => (
                    'A page for testing CBThemedTextView.'
                ),
                'title' => 'CBThemedTextView Test Page',
            ]
        );

        include __DIR__ . '/sections.php';

        CBDB::transaction(
            function () use (
                $spec
            ) {
                CBModels::save(
                    $spec
                );
            }
        );
    }
    /* CBInstall_configure() */



    /* -- functions -- */



    /**
     * @return CBID
     */
    static function
    ID(
    ): string {
        return 'f42f10168d15e025bb99231f034b5bf319da7388';
    }
    /* ID() */

}

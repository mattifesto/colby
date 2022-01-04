<?php

final class
CBTextView2TestPage {

    /* -- CBInstall interfaces -- */



    /**
     * This page can be used as a sandbox. When the site is updated, this page
     * will be resaved if it has either been changed by a user or if the spec
     * defined here has been updated.
     *
     * @return void
     */
    static function
    CBInstall_configure(
    ): void {
        CBDB::transaction(
            function () {
                CBModels::deleteByID(
                    CBTextView2TestPage::ID()
                );
            }
        );

        $spec = CBModelTemplateCatalog::fetchLivePageTemplate();

        CBModel::merge(
            $spec,
            (object)[
                'ID' => CBTextView2TestPage::ID(),
                'description' => (
                    'A page for testing and experimenting with CBTextView2.'
                ),
                'title' => 'CBTextView2 Test Page',
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
        return 'ab9a674ce554ce49b7a1d1415f219f8fcf8a3e1f';
    }
    /* ID() */

}

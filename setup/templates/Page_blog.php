<?php

/**
 * @TODO 2018_09_10
 *
 *      File should be renamed to Page_blog.php
 *      Class should be renamed to PREFIXPage_blog
 */
final class PREFIXPage_blog {

    /**
     * @return void
     */
    static function CBInstall_configure(): void {
        PREFIXPage_blog::installPage();
        PREFIXPage_blog::installMenuItem();
    }

    /**
     * @return ID
     */
    static function ID(): string {
        return RANDOMID;
    }

    /**
     * @return void
     */
    private static function installMenuItem(): void {
        $updater = CBModelUpdater::fetch(
            (object)[
                'ID' => PREFIXMenu_main::ID(),
            ]
        );

        $menuSpec = $updater->working;

        CBMenu::addOrReplaceItem(
            $menuSpec,
            (object)[
                'className' => 'CBMenuItem',
                'name' => 'blog',
                'text' => 'Blog',
                'URL' => '/blog/',
            ]
        );

        CBModelUpdater::save($updater);
    }

    /**
     * @return void
     */
    private static function installPage(): void {
        $updater = CBModelUpdater::fetch(
            CBModelTemplateCatalog::fetchLivePageTemplate(
                (object)[
                    'ID' => PREFIXPage_blog::ID(),
                    'classNameForKind' => 'PREFIXGeneratedPageKind',
                    'isPublished' => true,
                    'selectedMenuItemNames' => 'blog',
                    'title' => 'Blog',
                    'URI' => 'blog',
                ]
            )
        );

        $pageSpec = $updater->working;

        /* publicationTimeStamp */

        if (CBModel::valueAsInt($pageSpec, 'publicationTimeStamp') === null) {
            $pageSpec->publicationTimeStamp = time();
        }

        /* page title and description view */

        $sourceID = RANDOMID;

        CBSubviewUpdater::unshift(
            $pageSpec,
            'sourceID',
            $sourceID,
            (object)[
                'className' => 'CBPageTitleAndDescriptionView',
                'sourceID' => $sourceID,
            ]
        );

        /* page list view */

        $sourceID = RANDOMID;

        CBSubviewUpdater::push(
            $pageSpec,
            'sourceID',
            $sourceID,
            (object)[
                'className' => 'CBPageListView2',
                'classNameForKind' => 'PREFIXBlogPostPageKind',
                'sourceID' => $sourceID,
            ]
        );

        /* save */

        CBModelUpdater::save($updater);
    }
}

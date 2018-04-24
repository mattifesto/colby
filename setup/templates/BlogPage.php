<?php

final class PREFIXBlogPage {

    /**
     * @return void
     */
    static function CBInstall_configure(): void {
        $originalSpec = CBModels::fetchSpecByID(PREFIXBlogPage::ID());

        if (empty($originalSpec)) {
            $spec = CBModelTemplateCatalog::fetchLivePageTemplate();

            CBModel::merge($spec, (object)[
                'ID' => PREFIXBlogPage::ID(),
                'isPublished' => true,
                'publicationTimeStamp' => time(),
                'selectedMainMenuItemName' => 'blog',
                'URI' => 'blog',
                'sections' => [
                    (object)[
                        'className' => 'CBPageTitleAndDescriptionView',
                    ],
                    (object)[
                        'className' => 'CBPageListView2',
                        'classNameForKind' => 'PREFIXBlogPostPageKind',
                    ],
                ],
            ]);
        } else {
            $spec = CBModel::clone($originalSpec);
        }

        /* properties reset every update */

        CBModel::merge($spec, (object)[
            'className' => 'CBViewPage',
            'title' => 'Blog',
        ]);

        /* save if modified */

        if ($spec != $originalSpec) {
            CBDB::transaction(function () use ($spec) {
                CBModels::save($spec);
            });
        }
    }

    /**
     * @return ID
     */
    static function ID(): string {
        return RANDOMID;
    }
}

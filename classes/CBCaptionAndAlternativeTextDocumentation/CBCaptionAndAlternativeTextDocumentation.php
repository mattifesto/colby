<?php

final class CBCaptionAndAlternativeTextDocumentation {

    /* -- CBAdmin interfaces -- -- -- -- -- */



    /**
     * @return string
     */
    static function CBAdmin_getUserGroupClassName(): string {
        return 'CBAdministratorsUserGroup';
    }



    /**
     * @return [string]
     */
    static function CBAdmin_menuNamePath(): array {
        return [
            'help',
            'caption_alternativetext',
        ];
    }



    /**
     * @return void
     */
    static function CBAdmin_render(): void {
        CBHTMLOutput::pageInformation()->title = 'Caption and Alternative Text';

        echo '<div class="CBLightTheme">';

        $contentAsCommonMark = file_get_contents(
            Colby::flexpath(__CLASS__, 'md', cbsysdir())
        );

        CBView::renderSpec((object)[
            'className' => 'CBPageTitleAndDescriptionView',
        ]);

        CBView::renderSpec((object)[
            'className' => 'CBTextView2',
            'contentAsCommonMark' => $contentAsCommonMark,
        ]);

        echo '</div>';
    }

    /**
     * @return void
     */
    static function CBInstall_install(): void {
        $spec = CBModels::fetchSpecByID(CBHelpAdminMenu::ID());

        $spec->items[] = (object)[
            'className' => 'CBMenuItem',
            'name' => 'caption_alternativetext',
            'text' => 'Caption & Alternative Text',
            'URL' => '/admin/?c=CBCaptionAndAlternativeTextDocumentation',
        ];

        CBDB::transaction(function () use ($spec) {
            CBModels::save($spec);
        });
    }

    /**
     * @return [string]
     */
    static function CBInstall_requiredClassNames(): array {
        return ['CBHelpAdminMenu'];
    }
}

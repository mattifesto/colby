<?php

final class CBTitleAndDescriptionDocumentation {

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
            'title_description',
        ];
    }



    /**
     * @return void
     */
    static function CBAdmin_render(): void {
        CBHTMLOutput::pageInformation()->title = 'Title and Description';

        echo '<div class="CBLightTheme">';

        $contentAsMessageMarkup = file_get_contents(
            Colby::flexpath(__CLASS__, 'mmk', cbsysdir())
        );

        CBView::renderSpec((object)[
            'className' => 'CBPageTitleAndDescriptionView',
        ]);

        CBView::renderSpec((object)[
            'className' => 'CBMessageView',
            'markup' => $contentAsMessageMarkup,
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
            'name' => 'title_description',
            'text' => 'Title & Description',
            'URL' => '/admin/?c=CBTitleAndDescriptionDocumentation',
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

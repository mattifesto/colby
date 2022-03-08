<?php

final class
CBViewDocumentation
{
    /**
     * @return string
     */
    static function CBAdmin_getUserGroupClassName(): string {
        return 'CBDevelopersUserGroup';
    }



    /**
     * @return [string]
     */
    static function CBAdmin_menuNamePath(): array {
        return [
            'help',
        ];
    }



    /**
     * @return void
     */
    static function
    CBAdmin_render(
    ): void
    {
        CBHTMLOutput::pageInformation()->title =
        'CBView Documentation';

        CBView::renderSpec(
            (object)[
                'className' =>
                'CBPageTitleAndDescriptionView',
            ]
        );

        CBView::renderSpec(
            (object)[
                'className' =>
                'CBMessageView',

                'markup' =>
                file_get_contents(
                    __DIR__ .
                    '/CBViewDocumentation.mmk'
                ),
            ]
        );
    }
    // CBAdmin_render()



    /**
     * @return void
     */
    static function CBInstall_install(): void {
        $spec = CBModels::fetchSpecByID(CBHelpAdminMenu::ID());

        $spec->items[] = (object)[
            'className' => 'CBMenuItem',
            'name' => 'CBView',
            'text' => 'CBView',
            'URL' => '/admin/?c=CBViewDocumentation',
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

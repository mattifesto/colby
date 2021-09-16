<?php

final class
CB_StandardPageSettings {

    /* -- CBInstall interfaces -- */



    /**
     * @return void
     */
    static function
    CBInstall_install(
    ): void {
        CBPageSettingsCatalog::install(
            __CLASS__
        );
    }
    /* CBInstall_install() */



    /**
     * @return [string]
     */
    static function
    CBInstall_requiredClassNames(
    ): array {
        return [
            'CBPageSettingsCatalog'
        ];
    }
    /* CBInstall_requiredClassNames() */



    /* -- CBPageSettings interfaces -- */



    /**
     * @return [string]
     */
    static function
    CBPageSettings_htmlElementClassNames(
    ): array {
        return [
            'CB_UI',
        ];
    }
    /* CBPageSettings_htmlElementClassNames() */



    /**
     * @return [string]
     */
    static function
    CBPageSettings_requiredClassNames(
    ): array {
        return [
            'CBEqualizePageSettingsPart',
            'CBResponsiveViewportPageSettingsPart',
            'CBGoogleTagManagerPageSettingsPart',
            'CBFacebookPageSettingsPart',
        ];
    }
    /* CBPageSettings_requiredClassNames() */

}

<?php

final class CBStandardModels {
    const CBMenuIDForMainMenu = 'fa0a9625d16acb42a5f6fc94ff40b7e48658936b';
    const CBThemeIDForCBMenuViewForMainMenu = '9d5e09b9312025f64d9a6d430737eeb2236a89a7';

    /* @deprecated use CBWellKnownThemeForPageTitleAndDescription::ID */
    const CBThemeIDForCBPageTitleAndDescriptionView = '664d22662308f7443e4c3b43683d4934de087b86';

    /* @deprecated use CBWellKnownThemeForContent::ID */
    const CBThemeIDForCBTextViewForBodyText = '0d1bedea8d5e706950f1878ad3aff961ba36b631';

    /**
     * @return null
     */
    public static function install() {
        include __DIR__ . '/CBStandardModelsInstall.php';
    }
}

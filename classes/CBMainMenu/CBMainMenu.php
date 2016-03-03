<?php

/**
 * @deprecated use CBStandardModels
 */
final class CBMainMenu {

    const ID = CBStandardModels::CBMenuIDForMainMenu;

    /**
     * @return null
     */
    public static function install() {
        include __DIR__ . '/install.php';
    }
}

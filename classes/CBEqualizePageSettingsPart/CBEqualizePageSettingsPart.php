<?php

final class CBEqualizePageSettingsPart {

    /**
     * @return void
     */
    static function CBPageSettings_renderHeadElementHTML(): void { ?>
        <script src="<?= cbsysurl() . '/classes/CBEqualizePageSettingsPart/html5shiv.v362.js' ?>"></script>
        <script src="<?= cbsysurl() . '/classes/CBEqualizePageSettingsPart/es6-promise.auto.min.v362.js' ?>"></script>
        <script src="<?= Colby::flexpath(__CLASS__, 'v362.js', cbsysurl()) ?>"></script>
        <link rel="stylesheet" href="<?= Colby::flexpath(__CLASS__, 'v414.css', cbsysurl()) ?>">
    <?php }
}

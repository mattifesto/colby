<?php

final class CBEqualizePageSettingsPart {

    /**
     * html5shiv.js -> IE 11
     * es6-promise.auto.min.js -> IE 11
     * classList.min.js -> IE 11
     *
     * @return void
     */
    static function CBPageSettings_renderHeadElementHTML(): void { ?>
        <script src="<?= cbsysurl() . '/classes/CBEqualizePageSettingsPart/html5shiv.v362.js' ?>"></script>
        <script src="<?= cbsysurl() . '/classes/CBEqualizePageSettingsPart/es6-promise.auto.min.v362.js' ?>"></script>
        <script src="<?= cbsysurl() . '/classes/CBEqualizePageSettingsPart/classList.min.v438.js' ?>"></script>
        <script src="<?= Colby::flexpath(__CLASS__, 'v468.js', cbsysurl()) ?>"></script>
        <link rel="stylesheet" href="<?= Colby::flexpath(__CLASS__, 'v468.css', cbsysurl()) ?>">
    <?php }
}

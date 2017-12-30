<?php

final class CBEqualize {

    /**
     * @return void
     */
    static function CBHTMLOutput_renderHeadContent(): void { ?>
        <script src="<?= cbsysurl() . '/classes/CBEqualize/html5shiv.v362.js' ?>"></script>
        <script src="<?= Colby::flexpath(__CLASS__, 'v362.js', cbsysurl()) ?>"></script>
        <link rel="stylesheet" href="<?= Colby::flexpath(__CLASS__, 'v362.css', cbsysurl()) ?>">
    <?php }
}

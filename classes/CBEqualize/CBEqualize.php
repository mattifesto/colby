<?php

final class CBEqualize {

    /**
     * @return void
     */
    static function CBHTMLOutput_renderHeadContent(): void { ?>
        <script src="<?= CBSystemURL ?>/javascript/html5shiv.v362.js"></script>
        <script src="<?= CBSystemURL ?>/javascript/ColbyEqualize.v362.js"></script>
        <link rel="stylesheet" href="<?= CBSystemURL ?>/css/equalize.v362.css">
    <?php }
}

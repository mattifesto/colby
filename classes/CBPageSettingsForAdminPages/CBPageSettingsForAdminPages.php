<?php

final class CBPageSettingsForAdminPages {

    /**
     * @return  null
     */
    public static function renderEndOfBodyContent() { ?>
        <script src="<?= CBSystemURL ?>/javascript/Colby.js"></script>
    <?php }

    /**
     * @return  null
     */
    public static function renderHeadContent() { ?>
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <script src="<?= CBSystemURL ?>/javascript/html5shiv.js"></script>
        <script src="<?= CBSystemURL ?>/javascript/ColbyEqualize.js"></script>
        <link rel="stylesheet" href="<?= CBSystemURL ?>/css/equalize.css">
        <link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Source+Sans+Pro:400">
        <link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Source+Sans+Pro:700">
        <style>html { color: hsl(0, 0%, 20%); font-family: "Source Sans Pro"; }</style>
    <?php }
}

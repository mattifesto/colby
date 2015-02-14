<?php

class CPAdminSectionHeaderView {

    /**
     * @return stdClass
     */
    public static function compile($spec) {
        $model                  = new stdClass();
        $model->viewClassName   = __CLASS__;
        $model->title           = isset($spec->title) ? (string)$spec->title : '';
        $model->titleAsHTML     = ColbyConvert::textToHTML($model->title);

        return $model;
    }

    /**
     * @return void
     */
    public static function renderAsHTML($model) {
        ?>

        <header>
            <h1 style="text-align: center;"><?= $model->titleAsHTML ?></h1>
        </header>

        <?php
    }
}

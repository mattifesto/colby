<?php

final class CBEqualizePageSettingsPart {

    /* -- CBPageSettings interfaces -- -- -- -- -- */



    /**
     * @return void
     */
    static function CBPageSettings_renderHeadElementHTML(): void {
        $JavaScriptURL = Colby::flexpath(__CLASS__, 'v468.js', cbsysurl());
        $CSSURL = Colby::flexpath(__CLASS__, 'v468.css', cbsysurl());

        ?>
        <script src="<?= $JavaScriptURL ?>"></script>
        <link rel="stylesheet" href="<?= $CSSURL ?>">
        <?php
    }
    /* CBPageSettings_renderHeadElementHTML() */
}

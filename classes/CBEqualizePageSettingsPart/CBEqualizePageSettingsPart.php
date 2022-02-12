<?php

final class
CBEqualizePageSettingsPart {

    /* -- CBPageSettings interfaces -- -- -- -- -- */



    /**
     * @return void
     */
    static function
    CBPageSettings_renderHeadElementHTML(
    ): void {
        $CSSURL = Colby::flexpath(
            __CLASS__,
            'v675.55.css',
            cbsysurl()
        );

        ?>
        <link rel="stylesheet" href="<?= $CSSURL ?>">
        <?php
    }
    /* CBPageSettings_renderHeadElementHTML() */

}

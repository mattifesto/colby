<?php

final class
CB_CBView_MomentCreator {

    /* -- CBHTMLOutput interfaces -- */



    /**
     * @return [string]
     */
    static function
    CBHTMLOutput_JavaScriptURLs(
    ): array {
        return [
            Colby::flexpath(
                __CLASS__,
                'v675.45.js',
                cbsysurl()
            ),
        ];
    }
    /* CBHTMLOutput_JavaScriptURLs() */



    /**
     * @return [string]
     */
    static function
    CBHTMLOutput_requiredClassNames(
    ): array {
        return [
            'CB_CBView_Moment',
            'CB_UI',
            'CBAjax',
            'CBErrorHandler',
            'CBUIButton',
            'CBUIStringEditor2',
            'Colby',
        ];
    }
    /* CBHTMLOutput_requiredClassNames() */



    /* -- CBModel interfaces -- */



    static function
    CBModel_build(
        $viewSpec
    ): stdClass {
        return (object)[];
    }



    /* -- CBView interfaces -- */



    static function
    CBView_render(
        $viewModel
    ): void {
        ?>

        <div class="CB_CBView_MomentCreator"></div>

        <?php
    }

}

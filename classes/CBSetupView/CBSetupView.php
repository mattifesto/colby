<?php

/**
 * @deprecated 2022_01_29
 *
 *      Websites are no longer set up using this class, they are set up
 *      fully using the cbt command in terminal. This code should be
 *      removed during a larger task to remove the old setup code.
 */
final class
CBSetupView
{

    // -- CBCodeAdmin interfaces



    /**
     * @return [object]
     */
    static function
    CBCodeAdmin_searches(
    ): array
    {
        return
        [
            (object)[
                'CBCodeSearch_CBID' =>
                '1b70fd5c75bcaa7c0a0339bd9d94c0ae61ec01d7',

                'cbmessage' => <<<EOT

                    Websites are no longer set up using this method, they are
                    set up fully using the cbt command in terminal

                EOT,

                'regex' =>
                '\bCBSetupView\b',

                'severity' =>
                5,

                'title' =>
                'CBSetupView',

                'noticeVersion' => '2022_06_03_1654267756',
            ],
        ];
    }
    /* CBCodeAdmin_searches() */



    /* -- CBHTMLOutput interfaces -- -- -- -- -- */



    /**
     * @return [string]
     */
    static function CBHTMLOutput_JavaScriptURLs(): array {
        return [
            Colby::flexpath(__CLASS__, 'v636.js', cbsysurl()),
        ];
    }



    /**
     * @return [string]
     */
    static function CBHTMLOutput_requiredClassNames(): array {
        return [
            'CBAjax',
            'CBUI',
            'CBUIPanel',
            'CBUIStringEditor',
            'Colby',
        ];
    }
    /* CBHTMLOutput_requiredClassNames() */



    /**
     * @return [[<name>, <value>]]
     */
    static function CBHTMLOutput_JavaScriptVariables(): array {
        return [
            [
                'CBSetupView_suggestedWebsiteHostname',
                $_SERVER['HTTP_HOST'],
            ],
        ];
    }
    /* CBHTMLOutput_JavaScriptVariables() */



    /* -- CBModel interfaces -- -- -- -- -- */



    /**
     * @param object $viewSpec
     *
     * @return object
     */
    static function CBModel_build(
        stdClass $viewSpec
    ): stdClass {
        return (object)[];
    }
    /* CBModel_build() */



    /* -- CBView interfaces -- -- -- -- -- */



    /**
     * @param object $viewModel
     *
     * @return void
     */
    static function CBView_render(
        stdClass $viewModel
    ): void {
        ?>

        <div class="CBSetupView">
        </div>

        <?php
    }
    /* CBView_render() */

}

<?php

final class SCProductBuyViewTests {

    /* -- CBHTMLOutput interfaces -- -- -- -- -- */



    /**
     * @return [string]
     */
    static function CBHTMLOutput_JavaScriptURLs(): array {
        return [
            Colby::flexpath(
                __CLASS__,
                'v642.js',
                scliburl()
            ),
        ];
    }
    /* CBHTMLOutput_JavaScriptURLs() */



    /**
     * @return [string]
     */
    static function CBHTMLOutput_requiredClassNames(): array {
        return [
            'SCProductBuyView',
        ];
    }
    /* CBHTMLOutput_requiredClassNames() */



    /* -- CBTest interfaces -- -- -- -- -- */



    /**
     * @return [object]
     */
    static function CBTest_getTests(): array {
        return [
            (object)[
                'name' => 'render',
                'type' => 'server',
            ],
            (object)[
                'name' => 'render',
            ],
        ];
    }
    /* CBTest_getTests() */



    /* -- tests -- -- -- -- -- */



    /**
     * @return object
     */
    static function CBTest_render(): stdClass {
        ob_start();

        try {
            SCProductTests::installTestProducts();

            /* bad product code */

            $model = (object)[
                'className' => 'SCProductBuyView',
                'productCode' => 'SCProductBuyViewTests_bad_1',
            ];

            SCProductBuyView::CBView_render($model);


            /* good product code */

            $model = (object)[
                'className' => 'SCProductBuyView',
                'productCode' => SCProductTests::testProductCode1(),
            ];

            SCProductBuyView::CBView_render($model);
        } finally {
            ob_end_clean();

            SCProductTests::uninstallTestProducts();
        }

        return (object)[
            'succeeded' => true,
        ];
    }
    /* CBTest_render() */
}

<?php

final class SCShippingAddressEditorView_Tests {

    /* -- CBHTMLOutput interfaces -- -- -- -- -- */

    /**
     * @return [string]
     */
    static function CBHTMLOutput_JavaScriptURLs(): array {
        return [
            Colby::flexpath(__CLASS__, 'v106.1.js', scliburl()),
        ];
    }


    /**
     * @return [string]
     */
    static function CBHTMLOutput_requiredClassNames(): array {
        return [
            'CBTest',
            'SCShippingAddressEditorView',
        ];
    }


    /* -- CBTest interfaces -- -- -- -- -- */

    /**
     * @return [object]
     */
    static function CBTest_getTests(): array {
        return [
            (object)[
                'title' => (
                    'SCShippingAddressEditorView.storedShippingAddressModel()'
                ),
                'name' => 'storedShippingAddressModel',
            ],
        ];
    }
    /* CBTest_getTests() */
}

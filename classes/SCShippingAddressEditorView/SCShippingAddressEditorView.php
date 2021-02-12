<?php

final class SCShippingAddressEditorView {

    /* -- CBHTMLOutput interfaces -- -- -- -- -- */



    /**
     * @return [string]
     */
    static function CBHTMLOutput_CSSURLs(): array {
        return [
            Colby::flexpath(__CLASS__, 'v137.css', scliburl()),
        ];
    }



    /**
     * @return [string]
     */
    static function
    CBHTMLOutput_JavaScriptURLs(
    ): array {
        return [
            Colby::flexpath(
                __CLASS__,
                'v675.13.js',
                cbsysurl()
            ),
        ];
    }
    /* CBHTMLOutput_JavaScriptURLs() */



    /**
     * @return [[<global variable name>, <value>]]
     */
    static function CBHTMLOutput_JavaScriptVariables(): array {
        $preferencesModel = CBModelCache::fetchModelByID(
            SCPreferences::ID()
        );

        $orderKindClassName = CBModel::valueToString(
            $preferencesModel,
            'defaultOrderKindClassName'
        );

        if ($orderKindClassName === '') {
            $countryOptions = [];
        } else {
            $countryOptions = SCOrderKind::countryOptions(
                $orderKindClassName
            );
        }

        return [
            [
                'SCShippingAddressEditorView_countryOptions',
                $countryOptions,
            ]
        ];
    }
    /* CBHTMLOutput_JavaScriptVariables() */



    /**
     * @return [string]
     */
    static function
    CBHTMLOutput_requiredClassNames(
    ): array {
        return [
            'CBEvent',
            'CBModel',
            'CBUI',
            'CBUIMessagePart',
            'CBUINavigationView',
            'CBUISectionItem4',
            'CBUISelector',
            'CBUIStringEditor',
            'CBUIStringsPart',
            'SCShippingAddress',
        ];
    }
    /* CBHTMLOutput_requiredClassNames() */



    /* -- CBModel interfaces -- -- -- -- -- */



    /**
     * @param object $spec
     *
     * @return object
     */
    static function CBModel_build(
        stdClass $spec
    ): stdClass {
        return (object)[];
    }



    /* -- CBModel interfaces -- -- -- -- -- */



    /**
     * @param object $viewModel
     *
     * @return void
     */
    static function CBView_render(
        stdClass $viewModel
    ): void {
        ?>

        <div class="SCShippingAddressEditorView CBUIRoot">
        </div>

        <?php
    }
    /* CBView_render() */

}

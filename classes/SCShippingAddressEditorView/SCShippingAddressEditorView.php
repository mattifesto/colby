<?php

final class
SCShippingAddressEditorView
{
    /* -- CBHTMLOutput interfaces -- */



    /**
     * @return [string]
     */
    static function
    CBHTMLOutput_CSSURLs(
    ): array
    {
        return [
            Colby::flexpath(
                __CLASS__,
                'v137.css',
                cbsysurl()
            ),
        ];
    }
    // CBHTMLOutput_CSSURLs()



    /**
     * @return [string]
     */
    static function
    CBHTMLOutput_JavaScriptURLs(
    ): array
    {
        return [
            Colby::flexpath(
                __CLASS__,
                'v675.60.4.js',
                cbsysurl()
            ),
        ];
    }
    /* CBHTMLOutput_JavaScriptURLs() */



    /**
     * @return [[<name>, <value>]]
     */
    static function
    CBHTMLOutput_JavaScriptVariables(
    ): array
    {
        $preferencesModel =
        CBModelCache::fetchModelByID(
            SCPreferences::ID()
        );

        $orderKindClassName =
        CBModel::valueToString(
            $preferencesModel,
            'defaultOrderKindClassName'
        );

        if (
            $orderKindClassName === ''
        ) {
            $countryOptions =
            [];
        }

        else {
            $countryOptions =
            SCOrderKind::countryOptions(
                $orderKindClassName
            );
        }

        $specialInstructionCBMessage =
        SCPreferences::getSpecialInstructionCBMessage(
            $preferencesModel
        );

        return
        [
            [
                'SCShippingAddressEditorView_countryOptions',
                $countryOptions,
            ],
            [
                'SCShippingAddressEditorView_specialInstructionCBMessage_jsvariable',
                $specialInstructionCBMessage,
            ],
        ];
    }
    /* CBHTMLOutput_JavaScriptVariables() */



    /**
     * @return [string]
     */
    static function
    CBHTMLOutput_requiredClassNames(
    ): array
    {
        return [
            'CBEvent',
            'CBMessageView',
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

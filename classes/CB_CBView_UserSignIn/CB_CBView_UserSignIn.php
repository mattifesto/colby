<?php

final class
CB_CBView_UserSignIn {

    /* -- CBHTMLOutput interfaces -- */



    /**
     * @return [string]
     */
    static function
    CBHTMLOutput_CSSURLs(
    ): array {
        return [
            Colby::flexpath(
                __CLASS__,
                'v675.54.css',
                cbsysurl()
            ),
        ];
    }
    /* CBHTMLOutput_CSSURLs() */



    /**
     * @return [string]
     */
    static function
    CBHTMLOutput_JavaScriptURLs(
    ): array {
        return [
            Colby::flexpath(
                __CLASS__,
                'v675.54.js',
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
    ) {
        return [
            'CB_UI_BooleanEditor',
            'CB_UI_StringEditor',
            'CBAjax',
            'CBErrorHandler',
            'CBJavaScript',
            'CBMessageMarkup',
            'CBUIButton',

            'CB_UI',
        ];
    }
    /* CBHTMLOutput_requiredClassNames() */



    /* -- CBModel interfaces -- */



    /**
     * @param object $viewSpec
     *
     * @return object
     */
    static function
    CBModel_build(
        stdClass $viewSpec
    ): stdClass {
        $viewModel = (object)[];

        CB_CBView_UserSignIn::setDestinationURL(
            $viewModel,
            CB_CBView_UserSignIn::getDestinationURL(
                $viewSpec
            )
        );

        return $viewModel;
    }
    /* CBModel_build() */



    /* -- CBView interfaces -- */



    /**
     * @param object $viewModel
     *
     * @return void
     */
    static function
    CBView_render(
        stdClass $viewModel
    ): void {
        $destinationURLAsHTML = cbhtml(
            CB_CBView_UserSignIn::getDestinationURL(
                $viewModel
            )
        );

        ?>

        <div
            class="CB_CBView_UserSignIn_placeholder_element"
            data-destination-u-r-l="<?= $destinationURLAsHTML ?>"
        >
        </div>

        <?php
    }
    /* CBView_render() */



    /* -- accessors -- */



    /**
     * @param object $viewModel
     *
     * @return string
     */
    static function
    getDestinationURL(
        stdClass $viewModel
    ): string {
        $destinationURL = CBModel::valueToString(
            $viewModel,
            'CB_CBView_UserSignIn_destinationURL_property'
        );

        return $destinationURL;
    }
    /* getDestinationURL() */



    /**
     * @param stdClass $viewModel
     * @param string $newDestinationURL
     *
     * @return void
     */
    static function
    setDestinationURL(
        stdClass $viewModel,
        string $newDestinationURL
    ): void {
        $viewModel->CB_CBView_UserSignIn_destinationURL_property = (
            $newDestinationURL
        );
    }
    /* setDestinationURL() */

}

<?php

final class CBFacebookSignInView {

    /* -- CBHTMLOutput interfaces -- -- -- -- -- */



    /**
     * @return [string]
     */
    static function CBHTMLOutput_CSSURLs(): array {
        return [
            Colby::flexpath(__CLASS__, 'v571.css', cbsysurl()),
        ];
    }



    /**
     * @return [string]
     */
    static function CBHTMLOutput_requiredClassNames(): array {
        return [
            'CBUI',
        ];
    }



    /* -- CBModel interfaces -- -- -- -- -- */



    /**
     * @param object $spec
     *
     * @return object
     */
    static function CBModel_build(
        stdClass $spec
    ): stdClass {
        return (object)[
            'destinationURL' => CBModel::valueToString(
                $spec,
                'destinationURL'
            ),
        ];
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
        $facebookAppID = CBFacebookPreferences::getAppID();

        if ($facebookAppID === '') {
            return;
        }

        $destinationURL = CBModel::valueToString(
            $viewModel,
            'destinationURL'
        );

        $oauthURLAtFacebookWebsite = CBFacebook::oauthURLAtFacebookWebsite(
            $destinationURL
        );

        ?>

        <div class="CBFacebookSignInView CBUI_view">
            <a
                class="CBFacebookSignInView_button"
                href="<?= $oauthURLAtFacebookWebsite ?>"
            >
                Sign In with Facebook
            </a>
        </div>

        <?php
    }
    /* CBView_render() */

}

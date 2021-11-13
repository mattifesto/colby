<?php

final class
CB_CBView_AdSense {

    /* -- CBHTMLOutput interfaces -- */



    /**
     * @return [string]
     */
    static function
    CBHTMLOutput_CSSURLs(
    ) {
        return [
            Colby::flexpath(
                __CLASS__,
                'v675.39.css',
                cbsysurl()
            ),
        ];
    }
    /* CBHTMLOutput_CSSURLs() */



    /**
     * @return [string]
     */
    static function
    CBHTMLOutput_requiredClassNames() {
        return [
            'CBUI',
        ];
    }
    /* CBHTMLOutput_requiredClassNames() */



    /* -- CBInstall interfaces -- -- -- -- -- */



    /**
     * @return void
     */
    static function
    CBInstall_install(
    ): void {
        CBViewCatalog::installView(
            __CLASS__
        );
    }
    /* CBInstall_install() */



    /**
     * @return [string]
     */
    static function
    CBInstall_requiredClassNames(
    ): array {
        return [
            'CBViewCatalog',
        ];
    }
    /* CBInstall_requiredClassNames() */



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

        CB_CBView_AdSense::setClient(
            $viewModel,
            CB_CBView_AdSense::getClient(
                $viewSpec
            )
        );

        CB_CBView_AdSense::setSlot(
            $viewModel,
            CB_CBView_AdSense::getSlot(
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
        $sitePreferencesModel = CBModelCache::fetchModelByID(
            CBSitePreferences::ID()
        );

        $environment = CBSitePreferences::getEnvironment(
            $sitePreferencesModel
        );

        $isProductionWebsite = (
            $environment === 'CBSitePreferences_environment_production'
        );

        $client = CB_CBView_AdSense::getClient(
            $viewModel
        );

        $slot = CB_CBView_AdSense::getSlot(
            $viewModel
        );

        if (
            $slot === ''
        ) {
            return;
        }

        if (
            $client === ''
        ) {
            $adSensePublisherID = CBSitePreferences::getAdSensePublisherID(
                CBSitePreferences::model()
            );

            if (
                $adSensePublisherID === ''
            ) {
                return;
            }

            $client = "ca-{$adSensePublisherID}";
        }

        $src = (
            'https://pagead2.googlesyndication.com' .
            '/pagead/js/adsbygoogle.js' .
            "?client={$client}"
        );

        $classes = implode(
            ' ',
            [
                'CB_CBView_AdSense',
                'CBUI_CBView',
                'CBUI_padding_standard',
            ]
        );

        if (
            !$isProductionWebsite
        ) {
            $classes .= " CB_CBView_AdSense_notProduction";
        }

        echo <<<EOT

            <div class="{$classes}">
                <div class="CB_CBView_AdSense_content CBUI_viewContent">

        EOT;

        echo <<<EOT

            <script async src="{$src}" crossorigin="anonymous"></script>
            <ins class="adsbygoogle"
                 style="display:block"
                 data-ad-client="{$client}"
                 data-ad-slot="{$slot}"
                 data-ad-format="auto"
                 data-full-width-responsive="true"></ins>
            <script>
                 (adsbygoogle = window.adsbygoogle || []).push({});
            </script>

        EOT;

        echo <<<EOT

                </div>
            </div>

        EOT;
    }
    /* CBView_render() */



    /* -- accessors -- */



    /**
     * @param stdClass $viewModel
     *
     * @return string
     */
    static function
    getClient(
        stdClass $viewModel
    ): string {
        return trim(
            CBModel::valueToString(
                $viewModel,
                'CB_CBView_AdSense_client'
            )
        );
    }
    /* getClient() */



    /**
     * @param stdClass $viewModel
     * @param string $newClient
     *
     * @return void
     */
    static function
    setClient(
        stdClass $viewModel,
        string $newClient
    ): void {
        $viewModel->CB_CBView_AdSense_client = $newClient;
    }
    /* setClient() */



    /**
     * @param stdClass $viewModel
     *
     * @return string
     */
    static function
    getSlot(
        stdClass $viewModel
    ): string {
        return trim(
            CBModel::valueToString(
                $viewModel,
                'CB_CBView_AdSense_slot'
            )
        );
    }
    /* getSlot() */



    /**
     * @param stdClass $viewModel
     * @param string $newSlot
     *
     * @return void
     */
    static function
    setSlot(
        stdClass $viewModel,
        string $newSlot
    ): void {
        $viewModel->CB_CBView_AdSense_slot = $newSlot;
    }
    /* setSlot() */

}

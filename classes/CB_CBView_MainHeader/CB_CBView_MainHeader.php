<?php

final class
CB_CBView_MainHeader {

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
                'v675.60.css',
                cbsysurl()
            ),
        ];
    }
    /* CBHTMLOutput_CSSURLs() */



    /**
     * @return [string]
     */
    static function CBHTMLOutput_requiredClassNames() {
        return [
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

        CB_CBView_MainHeader::setContext(
            $viewModel,
            CB_CBView_MainHeader::getContext(
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
        $currentPrettyUsername = CB_Username::fetchCurrentUserPrettyUsername();

        if (
            $currentPrettyUsername === null
        ) {
            $userEmoji = '👤';
            $userURL = CBUser::getSignInPageURL();
        } else {
            $userEmoji = '👨';
            $userURL = "/user/{$currentPrettyUsername}/";
        }

        echo <<<EOT

            <header class="CB_CBView_MainHeader CB_UI">
                <div class="CB_CBView_MainHeader_group">

        EOT;

        $context = CB_CBView_MainHeader::getContext(
            $viewModel
        );

        CB_CBView_MainHeader::renderItem(
            '☰',
            null,
            'CB_CBView_MainHeader_menuButton',
            $context
        );

        CB_CBView_MainHeader::renderItem(
            '🏠',
            '/'
        );

        CB_CBView_MainHeader::renderItem(
            '🔍',
            '/search/'
        );

        CB_CBView_MainHeader::renderItem(
            $userEmoji,
            $userURL,
        );

        echo <<<EOT

            </div>
            <div class="CB_CBView_MainHeader_group">

        EOT;

        $isAdministator = CBUserGroup::currentUserIsMemberOfUserGroup(
            'CBAdministratorsUserGroup'
        );

        if ($isAdministator) {
            CB_CBView_MainHeader::renderItem(
                '🔨',
                '/admin/'
            );
        }

        $stripePreferencesModel = CBModelCache::fetchModelByID(
            SCStripePreferences::ID()
        );

        $livePublishableKey = SCStripePreferences::getLivePublishableKey(
            $stripePreferencesModel
        );

        if ($livePublishableKey !== null) {
            CB_CBView_MainHeader::renderItem(
                '🛍',
                '/view-cart/'
            );
        }

        echo <<<EOT

                </div>
            </header>

        EOT;
    }
    /* CBView_render() */



    /* -- accessors -- */



    /**
     * @param object $viewModel
     *
     * @return string|null
     *
     *      Returns null if the model has no context.
     */
    static function
    getContext(
        stdClass $viewModel
    ): ?string {
        return CBModel::valueAsName(
            $viewModel,
            'CB_CBView_MainHeader_context'
        );
    }
    /* getContext() */



    /**
     * @param object $viewModel
     * @param string|null $context
     *
     *      See CBConvert::valueIsName() for characters allowed in context
     *      string.
     *
     * @return void
     */
    static function
    setContext(
        stdClass $viewModel,
        ?string $context
    ): void {
        $context = CBConvert::valueAsName(
            $context
        );

        $viewModel->CB_CBView_MainHeader_context = $context;
    }
    /* setContext() */



    /* -- functions -- */



    /**
     * @param string $emoji
     * @param string|null $url
     * @param string $CSSClass
     *
     * @return void
     */
    private static function
    renderItem(
        string $emoji,
        ?string $url,
        string $CSSClass = '',
        ?string $context = null
    ): void {
        $emojiAsHTML = cbhtml(
            $emoji
        );

        $CSSClasses = [
            'CB_CBView_MainHeader_item'
        ];

        $CSSClass = CBConvert::valueAsName(
            $CSSClass
        );

        if (
            $CSSClass !== null
        ) {
            array_push(
                $CSSClasses,
                $CSSClass
            );

            if (
                $context !== null
            ) {
                array_push(
                    $CSSClasses,
                    $CSSClass . '_' . $context
                );
            }
        }

        if (
            $url !== null
        ) {
            $url = cbhtml($url);
            $tag = 'a';
            $hrefAttribute = "href=\"{$url}\"";
        } else {
            $tag = 'div';
            $hrefAttribute = '';
        }

        $CSSClasses = implode(
            ' ',
            $CSSClasses
        );

        echo <<<EOT

            <{$tag} class="{$CSSClasses}" {$hrefAttribute}>
                <div class="CB_CBView_MainHeader_icon">$emojiAsHTML</div>
            </{$tag}>

        EOT;
    }
    /* renderItem() */

}

<?php

final class
CB_CBView_MainHeader
{
    // -- CBHTMLOutput interfaces



    /**
     * @return [string]
     */
    static function
    CBHTMLOutput_CSSURLs(
    ): array
    {
        $arrayOfCSSURLs =
        [
            CBLibrary::buildLibraryClassFilePath(
                __CLASS__,
                '2022_11_14_1668468039',
                'css',
                cbsysurl()
            ),
        ];

        return $arrayOfCSSURLs;
    }
    // CBHTMLOutput_CSSURLs()



    /**
     * @return [string]
     */
    static function
    CBHTMLOutput_requiredClassNames(
    ): array
    {
        $arrayOfRequiredClassNames =
        [
            'CB_MaterialSymbols',
            'CB_UI',
        ];

        return $arrayOfRequiredClassNames;

    }
    // CBHTMLOutput_requiredClassNames()



    /* -- CBModel interfaces -- */



    /**
     * @param object $viewSpec
     *
     * @return object
     */
    static function
    CBModel_build(
        stdClass $viewSpec
    ): stdClass
    {
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
        $currentPrettyUsername =
        CB_Username::fetchCurrentUserPrettyUsername();

        $currentUserProfileImageModel =
        null;

        if (
            $currentPrettyUsername === null
        ) {
            $userURL =
            CBUser::getSignInPageURL();
        }

        else
        {
            $userURL =
            "/user/{$currentPrettyUsername}/";

            $currentUserCBID =
            ColbyUser::getCurrentUserCBID();

            $currentUserModel =
            CBModelCache::fetchModelByCBID(
                $currentUserCBID
            );

            $currentUserProfileImageModel =
            CBUser::getProfileImageModel(
                $currentUserModel
            );
        }

        echo
        <<<EOT

            <header class="CB_CBView_MainHeader CB_UI">
                <div class="CB_CBView_MainHeader_group">

        EOT;

        $context =
        CB_CBView_MainHeader::getContext(
            $viewModel
        );

        CB_CBView_MainHeader::renderItem(
            'menu',
            null,
            'CB_CBView_MainHeader_menuButton',
            $context,
            null,
            [
                'CB_MaterialSymbols_characters',
            ]
        );

        $websiteIconImageModel =
        CBSitePreferences::getIconImage(
            CBSitePreferences::model()
        );

        CB_CBView_MainHeader::renderItem(
            'home',
            '/',
            'CB_CBView_MainHeader_websiteItem_element',
            null, // context
            $websiteIconImageModel,
            [
                'CB_MaterialSymbols_characters',
            ]
        );

        CB_CBView_MainHeader::renderItem(
            'person',
            $userURL,
            'CB_CBView_MainHeader_personItem_element',
            null, // context
            $currentUserProfileImageModel,
            [
                'CB_MaterialSymbols_characters',
            ]
        );

        CB_CBView_MainHeader::renderItem(
            'search',
            '/search/',
            'CB_MaterialSymbols_characters'
        );

        echo
        <<<EOT

            </div>
            <div class="CB_CBView_MainHeader_group">

        EOT;

        $isAdministator =
        CBUserGroup::currentUserIsMemberOfUserGroup(
            'CBAdministratorsUserGroup'
        );

        if (
            $isAdministator
        ) {
            CB_CBView_MainHeader::renderItem(
                'settings',
                '/admin/',
                'CB_MaterialSymbols_characters'
            );
        }

        $stripePreferencesModel =
        CBModelCache::fetchModelByID(
            SCStripePreferences::ID()
        );

        $livePublishableKey =
        SCStripePreferences::getLivePublishableKey(
            $stripePreferencesModel
        );

        if (
            $livePublishableKey !== null
        ) {
            CB_CBView_MainHeader::renderItem(
                'shopping_bag',
                '/view-cart/',
                'CB_MaterialSymbols_characters'
            );
        }

        echo
        <<<EOT

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
    ): ?string
    {
        return
        CBModel::valueAsName(
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
        $context =
        CBConvert::valueAsName(
            $context
        );

        $viewModel->CB_CBView_MainHeader_context =
        $context;
    }
    /* setContext() */



    /* -- functions -- */



    /**
     * @param string $emoji
     * @param string|null $url
     * @param string $CSSClass
     * @param string|null $context
     * @param object|null $imageModel
     * @param [string] $additionalCSSClassesArgument
     *
     * @return void
     */
    private static function
    renderItem(
        string $emoji,
        ?string $url,
        string $CSSClass = '',
        ?string $context = null,
        ?object $imageModel = null,
        array $additionalCSSClassesArgument = []
    ): void
    {
        $emojiAsHTML =
        cbhtml(
            $emoji
        );

        $CSSClasses =
        $additionalCSSClassesArgument;

        array_unshift(
            $CSSClasses,
            'CB_CBView_MainHeader_item'
        );

        if (
            $imageModel === null
        ) {
            array_push(
                $CSSClasses,
                'CB_CBView_MainHeader_item_dropShadow'
            );
        }

        $CSSClass =
        CBConvert::valueAsName(
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
                    (
                        $CSSClass .
                        '_' .
                        $context
                    )
                );
            }
        }

        if (
            $url !== null
        ) {
            $url =
            cbhtml(
                $url
            );

            $tag =
            'a';

            $hrefAttribute =
            "href=\"{$url}\"";
        } else {
            $tag =
            'div';

            $hrefAttribute =
            '';
        }

        $CSSClasses =
        implode(
            ' ',
            $CSSClasses
        );

        echo
        <<<EOT

            <{$tag} class="{$CSSClasses}" {$hrefAttribute}>
                <div class="CB_CBView_MainHeader_icon">

        EOT;

        if (
            $imageModel !== null
        ) {
            CBImage::renderPictureElementWithImageInsideAspectRatioBox(
                $imageModel,
                'rs200clc200',
                32,
                32
            );
        }

        else
        {
            echo $emojiAsHTML;
        }

        echo
        <<<EOT

                </div>
            </{$tag}>

        EOT;
    }
    /* renderItem() */

}

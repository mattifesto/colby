<?php

final class
CB_View_UserProfile
{
    // CBHTMLOutput interfaces



    /**
     * @return [string]
     */
    static function
    CBHTMLOutput_CSSURLs(
    ): array
    {
        $cssURLs =
        [
            CBLibrary::buildLibraryClassFilePath(
                __CLASS__,
                '2022_08_25_1661435645',
                'css',
                cbsysurl()
            ),
        ];

        return $cssURLs;
    }
    // CBHTMLOutput_CSSURLs()



    /**
     * @return [string]
     */
    static function
    CBHTMLOutput_requiredClassNames(
    ): array
    {
        $requiredClassNames =
        [
            'CB_UI',
        ];

        return $requiredClassNames;
    }
    // CBHTMLOutput_requiredClassNames()



    // CBModel interfaces



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
        $viewModel =
        (object)[];

        CB_View_UserProfile::setUserModelCBID(
            $viewModel,
            CB_View_UserProfile::getUserModelCBID(
                $viewSpec
            )
        );

        return $viewModel;
    }
    // CBModel_build()



    // CBView interfaces



    /**
     * @param object $viewModel
     *
     * @return void
     */
    static function
    CBView_render(
        stdClass $viewModel
    ): void
    {
        $userModelCBID =
        CB_View_UserProfile::getUserModelCBID(
            $viewModel
        );

        if (
            $userModelCBID === null
        ) {
            return;
        }

        $userModel =
        CBModelCache::fetchModelByCBID(
            $userModelCBID
        );

        if (
            $userModel === null
        ) {
            return;
        }



        // open

        echo
        '<div class="CB_View_UserProfile_rootElement">',
        '<div class="CB_View_UserProfile_contentElement">';



        // full name

        $userFullName =
        CBUser::getName(
            $userModel
        );

        echo
        '<div class="CB_View_UserProfile_fullNameElement">',
        cbhtml(
            $userFullName
        ),
        '</div>';



        // bio

        $userBio =
        CBUser::getBio(
            $userModel
        );

        echo
        '<div class="CB_View_UserProfile_bioElement">',
        cbhtml(
            $userBio
        ),
        '</div>';



        // links

        $userProfileLinkArray =
        CBUser::getProfileLinkArray(
            $userModel
        );

        $linkModels =
        CB_Link_Array::getLinks(
            $userProfileLinkArray
        );

        $visibleLinkAnchors =
        array_reduce(
            $linkModels,
            function (
                $visibleLinkAnchors,
                $linkModel
            ): array
            {
                $text =
                CB_Link::getText(
                    $linkModel
                );

                if (
                    $text ===
                    ''
                ) {
                    return $visibleLinkAnchors;
                }

                $casualURL =
                CB_Link::getURL(
                    $linkModel
                );

                $actualURL =
                CB_URL::convertCasualURLToActualURL(
                    $casualURL
                );

                if (
                    $actualURL ===
                    ''
                ) {
                    return $visibleLinkAnchors;
                }

                array_push(
                    $visibleLinkAnchors,
                    '<a href="' .
                    cbhtml(
                        $actualURL
                    ) .
                    '">' .
                    cbhtml(
                        $text
                    ) .
                    '</a>'
                );

                return $visibleLinkAnchors;
            },
            []
        );

        if (
            count($visibleLinkAnchors) > 0
        ) {
            echo
            '<div class="CB_View_UserProfile_linksElement">';

            echo
            implode(
                " | ",
                $visibleLinkAnchors
            );

            echo
            '</div>';
        }

        // edit profile

        $currentUseModelCBID =
        ColbyUser::getCurrentUserCBID();

        if (
            $userModelCBID ===
            $currentUseModelCBID
        ) {
            echo
            '<div class="CB_View_UserProfile_editProfileElement">',
            '<a href="/colby/user/">',
            'edit profile',
            '</a>',
            '</div>';
        }



        // close

        echo
        '</div></div>';
    }
    // CBView_render()



    // accessors



    /**
     * @param object $viewModel
     *
     * @return CBID|null
     */
    static function
    getUserModelCBID(
        stdClass $viewModel
    ): string
    {
        $userModelCBID =
        CBModel::valueAsCBID(
            $viewModel,
            'CB_View_UserProfile_userModelCBID_property'
        );

        return $userModelCBID;
    }
    // getUserModelCBID()



    /**
     * @param object $viewModel
     * @param CBID|null $newUserModelCBID
     *
     * @return void
     */
    static function
    setUserModelCBID(
        stdClass $viewModel,
        ?string $newUserModelCBID
    ): void
    {
        $viewModel->CB_View_UserProfile_userModelCBID_property =
        $newUserModelCBID;
    }
    // setUserModelCBID()

}
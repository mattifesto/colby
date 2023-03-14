<?php

/**
 * @NOTE 2022_10_05_1664977941
 *
 *      This view is being developed to render a more refined view of a moment
 *      that is similar to the way a moment would be shown in an art gallery
 *      and presenting the image and text in a very positive context.
 *
 *      The first caller is the CB_CBView_MostRecentUserMoment.
 */
final class
CB_View_Moment2
{
    // -- CBAdmin_CBDocumentationForClass interfaces



    static function
    CBAdmin_CBDocumentationForClass_render(
    ): void
    {
        $currentUserModelCBID =
        ColbyUser::getCurrentUserCBID();

        if (
            $currentUserModelCBID
            === null
        ) {
            $messageViewSpec =
            CBModel::createSpec(
                'CBMessageView'
            );

            $cbmessage = <<<EOT

                You must be logged in to see a list of your most recent moments
                on this page.

            EOT;

            CBMessageView::setCBMessage(
                $messageViewSpec,
                $cbmessage
            );

            CBView::renderSpec(
                $messageViewSpec
            );

            return;
        }

        $momentModelCBIDs =
        CB_Moment::fetchMostRecentMomentModelCBIDsForUserModelCBID(
            $currentUserModelCBID
        );

        foreach(
            $momentModelCBIDs
            as $momentModelCBID
        ) {
            $moment2ViewSpec =
            CBModel::createSpec(
                'CB_View_Moment2'
            );

            CB_View_Moment2::setMomentModelCBID(
                $moment2ViewSpec,
                $momentModelCBID
            );

            CBView::renderSpec(
                $moment2ViewSpec
            );
        }
    }
    // CBAdmin_CBDocumentationForClass_render()



    // -- CBHTMLOutput interfaces



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
                '2023_03_14_1678827449',
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
    CBHTMLOutput_JavaScriptURLs(
    ): array
    {
        $javaScriptURLs =
        [
            CBLibrary::buildLibraryClassFilePath(
                __CLASS__,
                '2023_03_14_1678827474',
                'js',
                cbsysurl()
            ),
        ];

        return $javaScriptURLs;
    }
    // CBHTMLOutput_JavaScriptURLs()



    /**
     * @return [string]
     */
    static function
    CBHTMLOutput_requiredClassNames(
    ): array
    {
        $requiredClassNames =
        [
            'CB_Moment',
            'CB_UI',
            'CBErrorHandler',
            'CBImage',
            'CBModel',
            'CBUser',
            'Colby',
        ];

        return $requiredClassNames;
    }
    // CBHTMLOutput_requiredClassNames()



    // -- CBModel interfaces



    /**
     * @param object $viewSpec
     *
     * @return object
     */
    static function
    CBModel_build(
        stdClass $viewSpecArgument
    ): stdClass
    {
        $viewModel =
        (object)[];

        CB_View_Moment2::setMomentModelCBID(
            $viewModel,
            CB_View_Moment2::getMomentModelCBID(
                $viewSpecArgument
            )
        );

        return $viewModel;
    }
    // CBModel_build()



    // -- CBView interfaces



    /**
     * @param object $viewModel
     *
     * @return void
     */
    static function
    CBView_render(
        stdClass $viewModelArgument
    ): void
    {
        $momentModelCBID =
        CB_View_Moment2::getMomentModelCBID(
            $viewModelArgument
        );

        if (
            $momentModelCBID
            === null
        ) {
            return;
        }

        $momentModel =
        CBModelCache::fetchModelByCBID(
            $momentModelCBID
        );

        if (
            $momentModel
            === null
        ) {
            return;
        }

        echo <<<EOT

            <div class="CB_View_Moment2_root_element">

        EOT;

        CB_View_Moment2::renderImage(
            $momentModel
        );

        echo
        CBConvert::stringToCleanLine(<<< EOT

            <div class="CB_View_Moment2_textCard_element">

        EOT);

        CB_View_Moment2::renderText(
            $momentModel
        );

        CB_View_Moment2::renderNameAndDate(
            $momentModel
        );

        CB_View_Moment2::renderLinks(
            $momentModel
        );

        echo <<<EOT

                </div>
            </div>

        EOT;
    }
    // CBView_render()



    // -- accessors



    static function
    getMomentModelCBID(
        stdClass $viewModelArgument
    ): ?string
    {
        $momentModelCBID =
        CBModel::valueAsCBID(
            $viewModelArgument,
            'CB_View_Moment2_momentModelCBID_property'
        );

        return $momentModelCBID;
    }
    // getMomentModelCBID()



    /**
     * @param object $viewModelArgument
     * @param CBID|null $momentModelCBIDArgument
     *
     * @return void
     */
    static function
    setMomentModelCBID(
        stdClass $viewModelArgument,
        ?string $momentModelCBIDArgument
    ): void
    {
        if (
            $momentModelCBIDArgument
            !== null
        ) {
            if (
                CBID::valueIsCBID(
                    $momentModelCBIDArgument
                )
                !== true
            ) {
                $momentModelCBIDArgumentAsJSON =
                json_encode(
                    $momentModelCBIDArgument
                );

                throw
                new CBExceptionWithValue(
                    CBConvert::stringToCleanLine(<<<EOT

                        The value ${momentModelCBIDArgumentAsJSON} was passed
                        as the moment model CBID argument and is not a valid
                        moment model CBID.

                    EOT),
                    $momentModelCBIDArgument,
                    '35fbefec4fcf09c804a26afa817e78ab2c32bd10'
                );
            }
        }

        $viewModelArgument->CB_View_Moment2_momentModelCBID_property =
        $momentModelCBIDArgument;
    }
    // setMomentModelCBID()



    // -- functions



    /**
     * @param object $momentModelArgument
     *
     * @return string
     */
    private static function
    generateLinkHTMLForEmail(
        stdClass $momentModelArgument
    ): string
    {
        $momentModelCBID =
        CBModel::getCBID(
            $momentModelArgument
        );

        $momentText =
        CB_Moment::getText(
            $momentModelArgument
        );

        $body =
        rawurlencode(
            $momentText .
            "\n\n" .
            cbsiteurl() .
            "/moment/${momentModelCBID}/"
        );

        $href =
        cbhtml(
            "mailto:?subject=Moment&body=${body}"
        );

        $linkHTML =
        <<<EOT

        <a
            href="${href}"
            title="share via email"
        >
            email
        </a>

        EOT;

        return $linkHTML;
    }
    // generateLinkHTMLForEmail()



    /**
     * @param object $momentModelArgument
     *
     * @return string
     */
    private static function
    generateLinkHTMLForMore(
        stdClass $momentModelArgument
    ): string
    {
        $momentModelCBID =
        CBModel::getCBID(
            $momentModelArgument
        );

        $linkHTML =
        <<<EOT

        <a
            href="/moment/${momentModelCBID}/"
            title="go to moment page"
        >
            more
        </a>

        EOT;

        return $linkHTML;
    }
    // generateLinkHTMLForMore()



    /**
     * @param object $momentModelArgument
     *
     * @return string
     */
    private static function
    generateLinkHTMLForUser(
        stdClass $momentModelArgument,
        string $textContent = ''
    ): string
    {
        $authorUserModelCBID =
        CB_Moment::getAuthorUserModelCBID(
            $momentModelArgument
        );

        $authorUserModel =
        CBModels::fetchModelByCBID(
            $authorUserModelCBID
        );

        $userPrettyUsernameAsHTML =
        cbhtml(
            CBUser::getPrettyUsername(
                $authorUserModel
            )
        );

        $userPageURLAsHTML =
        "/user/" .
        $userPrettyUsernameAsHTML .
        '/';

        if (
            $textContent ===
            ''
        ) {
            $textContent =
            "@${userPrettyUsernameAsHTML}";
        }

        $textContentAsHTML =
        cbhtml(
            $textContent
        );

        $linkHTML =
        <<<EOT
            <a
                href="${userPageURLAsHTML}"
            >
                ${textContentAsHTML}
            </a>

        EOT;

        return $linkHTML;
    }
    // generateLinkHTMLForUser()



    /**
     * @param object $momentModel
     *
     * @return void
     */
    private static function
    renderImage(
        stdClass $momentModelArgument
    ): void
    {
        $imageModel =
        CB_Moment::getImage(
            $momentModelArgument
        );

        if (
            $imageModel === null
        ) {
            return;
        }

        $alternativeText =
        CB_Moment::getImageAlternativeText(
            $momentModelArgument
        );

        if (
            $alternativeText ===
            ''
        ) {
            $alternativeText =
            'Image';
        }

        $momentPageURLAsHTML =
        cbhtml(
            cbsiteurl() .
            CBModel::toURLPath(
                $momentModelArgument
            )
        );

        echo
        CBConvert::stringToCleanLine(<<<EOT

            <a
                href="${momentPageURLAsHTML}"
                style="display: block;"
            >

        EOT);

        CBImage::renderPictureElementWithMaximumDisplayWidthAndHeight(
            $imageModel,
            'rh800rw2560',
            1280,
            400,
            $alternativeText
        );

        echo '</a>';
    }
    // renderImage()



    /**
     * @param object $momentModel
     *
     * @return void
     */
    private static function
    renderLinks(
        stdClass $momentModelArgument
    ): void
    {
        $arrayOfLinkHTMLs =
        [];

        array_push(
            $arrayOfLinkHTMLs,
            CB_View_Moment2::generateLinkHTMLForMore(
                $momentModelArgument
            )
        );

        array_push(
            $arrayOfLinkHTMLs,
            CB_View_Moment2::generateLinkHTMLForUser(
                $momentModelArgument
            )
        );

        array_push(
            $arrayOfLinkHTMLs,
            CB_View_Moment2::generateLinkHTMLForEmail(
                $momentModelArgument
            )
        );

        echo
        '<div class="CB_View_Moment2_links_element">';

        echo
        implode(
            ' • ',
            $arrayOfLinkHTMLs
        );

        echo
        '</div>';
    }
    // renderLinks()



    /**
     * @param object $momentModelArgument
     *
     * @return void
     */
    private static function
    renderNameAndDate(
        stdClass $momentModelArgument
    ): void
    {
        $authorUserModelCBID =
        CB_Moment::getAuthorUserModelCBID(
            $momentModelArgument
        );

        $authorUserModel =
        CBModelCache::fetchModelByCBID(
            $authorUserModelCBID
        );

        $userFullName =
        CBUser::getName(
            $authorUserModel
        );

        $userFullNameLinkHTML =
        CB_View_Moment2::generateLinkHTMLForUser(
            $momentModelArgument,
            $userFullName
        );

        $cbtimestamp =
        CB_Moment::getCBTimestamp(
            $momentModelArgument
        );

        $unixTimestamp =
        CB_Timestamp::getUnixTimestamp(
            $cbtimestamp
        );

        /**
         * This class name will determine how the time is formatted.
         */
        $timeElementClassName =
        'Colby_time_element_style_moment';

        $timestampElementAsHTML =
        ColbyConvert::timestampToHTML(
            $unixTimestamp,
            '',
            $timeElementClassName
        );

        echo
        CBConvert::stringToCleanLine(<<<EOT

            <div class="CB_View_Moment2_nameAndDate_element">

                <div>${userFullNameLinkHTML}</div>
                <div>${timestampElementAsHTML}</div>

            </div>

        EOT);
    }
    // renderNameAndDate()



    /**
     * @param object $momentModelArgument
     *
     * @return void
     */
    private static function
    renderText(
        stdClass $momentModelArgument
    ): void
    {
        $text =
        CB_Moment::getText(
            $momentModelArgument
        );

        if (
            trim($text)
            === ''
        ) {
            return;
        }

        echo
        '<div class="CB_View_Moment2_text_element">';

        echo
        cbhtml(
            $text
        );

        echo
        '</div>';
    }
    // renderText()

}

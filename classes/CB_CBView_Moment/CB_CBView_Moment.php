<?php

final class
CB_CBView_Moment
{
    /* -- CBHTMLOutput interfaces -- */



    /**
     * @return [string]
     */
    static function
    CBHTMLOutput_CSSURLs(
    ): array
    {
        return
        [
            Colby::flexpath(
                __CLASS__,
                'v675.62.css',
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
    ): array
    {
        return
        [
            Colby::flexpath(
                __CLASS__,
                'v675.68.js',
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
    ): array
    {
        return
        [
            'CB_Moment',
            'CBAjax',
            'CBConvert',
            'CBImage',
            'CBErrorHandler',
            'CBModel',
            'CBUIButton',
            'CBUIPanel',
            'CBUser',
            'Colby',

            'CB_UI',
            'CBAdministratorsUserGroup',
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
    ): stdClass
    {
        $viewModel =
        (object)[];

        CB_CBView_Moment::setMomentModelCBID(
            $viewModel,
            CB_CBView_Moment::getMomentModelCBID(
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
    ): void
    {
        $momentModelCBID =
        CB_CBView_Moment::getMomentModelCBID(
            $viewModel
        );

        $momentModel =
        CBModelCache::fetchModelByID(
            $momentModelCBID
        );

        CB_CBView_Moment::renderFullSizeMoment(
            $momentModel
        );
    }
    /* CBView_render() */



    /* -- accessors -- */



    /**
     * @param object $viewModel
     *
     * @return CBID|null
     */
    static function
    getMomentModelCBID(
        stdClass $viewModel
    ): ?string
    {
        return
        CBModel::valueAsCBID(
            $viewModel,
            'CB_CBView_Moment_momentModelCBID_property'
        );
    }
    /* getMomentModelCBID() */



    /**
     * @param object $viewModel
     * @param string $newMomentModelCBID
     *
     * @return void
     */
    static function
    setMomentModelCBID(
        stdClass $viewModel,
        string $newMomentModelCBID
    ): void
    {
        if (
            !CBID::valueIsCBID(
                $newMomentModelCBID
            )
        )
        {
            throw new InvalidArgumentException(
                'newMomentModelCBID'
            );
        }

        $viewModel->CB_CBView_Moment_momentModelCBID_property =
        $newMomentModelCBID;
    }
    /* setMomentModelCBID() */



    /* -- functions -- */



    /**
     * @param object $momentModel
     * @param bool $shouldIncludeLinksToMomentPage
     *
     * @return void
     */
    static function
    renderFullSizeMoment(
        stdClass $momentModel,
        bool $shouldIncludeLinksToMomentPage = false
    ): void
    {
        $momentModelAsJSONAsHTML =
        cbhtml(
            json_encode(
                $momentModel
            )
        );

        echo
        CBConvert::stringToCleanLine(<<<EOT

            <div
                class=
                "
                    CB_CBView_Moment_root_element
                    CB_CBView_Moment_momentPage
                    CB_CBView_Moment_uninitialized
                "

                data-momentmodelasjson0959d46fd2=
                "${momentModelAsJSONAsHTML}"
            >
                <div
                    class="CB_CBView_Moment_content_element"
                >

        EOT);

        CB_CBView_Moment::renderHeader(
            $momentModel
        );

        CB_CBView_Moment::renderText(
            $momentModel
        );

        CB_CBView_Moment::renderLargeImage(
            $momentModel,
            $shouldIncludeLinksToMomentPage
        );

        echo
        '</div></div>';
    }
    // renderFullSizeMoment()



    private static function
    renderHeader(
        stdClass $momentModel
    ): void
    {
        $authorUserModelCBID =
        CB_Moment::getAuthorUserModelCBID(
            $momentModel
        );

        $authorUserModel =
        CBModels::fetchModelByCBID(
            $authorUserModelCBID
        );

        $userFullNameAsHTML =
        cbhtml(
            CBUser::getName(
                $authorUserModel
            )
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

        $cbtimestamp =
        CB_Moment::getCBTimestamp(
            $momentModel
        );

        $unixTimestamp =
        CB_Timestamp::getUnixTimestamp(
            $cbtimestamp
        );

        $timestampElementAsHTML =
        ColbyConvert::timestampToHTML(
            $unixTimestamp,
            '',
            'Colby_time_element_style_moment'
        );

        $momentModelCBID =
        CBModel::getCBID(
            $momentModel
        );

        $momentURLAsHTML =
        cbhtml(
            "/moment/${momentModelCBID}/"
        );

        echo
        CBConvert::stringToCleanLine(<<<EOT

            <div class="CB_CBView_Moment_header_element">

                <div class="CB_CBView_Moment_information_element">

                    <a
                        class="CB_CBView_Moment_userLink_element"
                        href="${userPageURLAsHTML}"
                    >
                        <span class="CB_CBView_Moment_fullName_element">
                            ${userFullNameAsHTML}
                        </span>

                        <span class="CB_CBView_Moment_prettyUsername_element">
                            @${userPrettyUsernameAsHTML}
                        </span>
                    </a>

                    <span>
                    •
                    </span>

                    <a
                        class="CB_CBView_Moment_timeContainer_element"
                        href="${momentURLAsHTML}"
                    >
                        ${timestampElementAsHTML}
                    </a>

                </div>

                <div class="CB_CBView_Moment_ellipsis_element">

                    &nbsp;

                </div>

            </div>

        EOT);
    }
    // renderHeader()



    /**
     * @param object $momentModel
     * @param object $shouldIncludeLinksToMomentPage
     *
     * @return void
     */
    private static function
    renderLargeImage(
        $momentModel,
        bool $shouldIncludeLinksToMomentPage = false
    ): void
    {
        $imageModel =
        CB_Moment::getImage(
            $momentModel
        );

        if (
            $imageModel === null
        ) {
            return;
        }

        if (
            $shouldIncludeLinksToMomentPage
        ) {
            $momentModelCBID =
            CBModel::getCBID(
                $momentModel
            );

            $momentURLAsHTML =
            cbhtml(
                "/moment/${momentModelCBID}/"
            );

            echo
            '<a href="',
            $momentURLAsHTML,
            '" ',
            'class="CB_CBView_Moment_imageLinkToMomentPage_element" ',
            'style="display: block;" ',
            '>';
        }

        else
        {
            echo
            '<div ',
            'class = "CB_CBView_Moment_imageLinkToMomentPage_element"',
            '>';
        }

        $alternativeText =
        "Image";

        CBImage::renderPictureElementWithMaximumDisplayWidthAndHeight(
            $imageModel,
            'rw1600',
            800,
            2400,
            $alternativeText
        );

        if (
            $shouldIncludeLinksToMomentPage
        ) {
            echo
            '</a>';
        }

        else
        {
            echo
            '</div>';
        }
    }
    // renderLargeImage()



    /**
     * @param object $momentModel
     *
     * @return void
     */
    private static function
    renderText(
        stdClass $momentModel
    ): void
    {
        echo
        '<div class="CB_CBView_Moment_text_element">';

        $text =
        CB_Moment::getText(
            $momentModel
        );

        echo
        cbhtml(
            $text
        );

        echo
        '</div>';
    }
    // renderText()
}

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
                'v675.61.4.css',
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
                'v675.61.4.js',
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
            'CBImage',
            'CBErrorHandler',
            'CBModel',
            'CBUser',
            'Colby',

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
     *
     * @return void
     */
    static function
    renderFullSizeMoment(
        stdClass $momentModel
    ): void
    {
        echo CBConvert::stringToCleanLine(<<<EOT

            <div
                class="
                    CB_CBView_Moment_root_element
                    CB_CBView_Moment_momentPage
                "
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

        $imageModel =
        CB_Moment::getImage(
            $momentModel
        );

        if (
            $imageModel !== null
        ) {
            $alternativeText = "Image";

            CBImage::renderPictureElementWithMaximumDisplayWidthAndHeight(
                $imageModel,
                'rw1600',
                800,
                2400,
                $alternativeText
            );
        }

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

        $timestampHTML =
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
                <a
                    class="CB_CBView_Moment_userLink_element"
                    href="${userPageURLAsHTML}"
                >
                    <span class="CB_CBView_Moment_fullName_element">
                        ${userFullNameAsHTML}
                    </span>

                    @${userPrettyUsernameAsHTML}
                </a>
                •
                <a
                    class="CB_CBView_Moment_timeContainer_element"
                    href="${momentURLAsHTML}"
                >
                    ${timestampHTML}
                </a>
            </div>

        EOT);
    }
    // renderHeader()



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

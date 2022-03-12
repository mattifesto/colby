<?php

final class
CBArtworkView
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
                'v675.60.css',
                cbsysurl()
            ),
        ];
    }
    /* CBHTMLOutput_CSSURLs() */



    /* -- CBInstall interfaces -- */



    /**
     * @return void
     */
    static function
    CBInstall_install(
    ): void
    {
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
    ): array
    {
        return [
            'CBViewCatalog',
        ];
    }
    /* CBInstall_requiredClassNames() */



    /* -- CBModel interfaces -- */



    /**
     * @param object $spec
     *
     *      {
     *          alternativeText: string
     *          captionAsCBMessage: string
     *          captionAsMarkdown: string (deprecated)
     *          image: object
     *
     *              CBImage spec
     *
     *          renderImageOnly: bool
     *
     *              This setting will ensure that there are no links rendered
     *              inside of this view element. Use this when rendering an
     *              artwork view inside of another element that is a link.
     *
     *          size: string
     *
     *              The maximum width of the image in retina pixels. rw1600
     *              (800pt) is the default.
     *
     *              rw320|rw640|rw960|rw1280|rw1600|rw1920|rw2560|original|page
     *      }
     *
     * @return object
     */
    static function
    CBModel_build(
        stdClass $spec
    ): stdClass
    {
        $model = (object)[
            'alternativeText' =>
            trim(
                CBModel::valueToString($spec, 'alternativeText')
            ),

            'CSSClassNames' =>
            CBModel::valueToNames(
                $spec,
                'CSSClassNames'
            ),

            'renderImageOnly' =>
            CBModel::valueToBool(
                $spec,
                'renderImageOnly'
            ),

            'size' =>
            CBModel::valueAsName(
                $spec,
                'size'
            ),
        ];


        /* image */

        $imageSpec = CBModel::valueAsModel(
            $spec,
            'image',
            [
                'CBImage',
            ]
        );

        if (
            $imageSpec !== null
        ) {
            $model->image = CBModel::build(
                $imageSpec
            );
        }


        /* caption */

        $captionAsCBMessage = CBModel::valueToString(
            $spec,
            'captionAsCBMessage'
        );

        if (
            trim($captionAsCBMessage) !== ''
        ) {
            $model->captionAsHTML = CBMessageMarkup::messageToHTML(
                $captionAsCBMessage
            );
        }

        else {
            /**
             * @deprecated 2019_10_01
             */

            $captionAsMarkdown = CBModel::valueToString(
                $spec,
                'captionAsMarkdown'
            );

            $parsedown = new Parsedown();

            $model->captionAsHTML = $parsedown->text(
                $captionAsMarkdown
            );

            $captionAsCBMessage = CBMessageMarkup::stringToMessage(
                $captionAsMarkdown
            );
        }

        $model->captionAsCBMessage = $captionAsCBMessage;

        if (
            empty($model->alternativeText)
        ) {
            $model->alternativeText = mb_substr(
                CBMessageMarkup::messageToText(
                    $captionAsCBMessage
                ),
                0,
                100
            );
        }

        /* done */

        return $model;
    }
    /* CBModel_build() */



    /**
     * @param object $spec
     *
     * @return object
     */
    static function
    CBModel_upgrade(
        stdClass $spec
    ): stdClass
    {
        $imageSpec = CBModel::valueAsObject(
            $spec,
            'image'
        );

        if (
            $imageSpec !== null
        ) {
            $spec->image = CBImage::fixAndUpgrade(
                $imageSpec
            );
        }

        return $spec;
    }
    /* CBModel_upgrade() */



    /**
     * @param object $model
     *
     * @return string
     */
    static function
    CBModel_toSearchText(
        stdClass $model
    ): string
    {
        $alternativeText = CBModel::valueToString(
            $model,
            'alternativeText'
        );

        $captionAsText = CBMessageMarkup::messageToText(
            CBModel::valueToString(
                $model,
                'captionAsCBMessage'
            )
        );

        return "{$alternativeText} {$captionAsText}";
    }
    /* CBModel_toSearchText() */



    /**
     * @param string? $model->alternativeText
     * @param string? $model->captionAsHTML
     * @param string? $model->captionAsCBMessage
     *
     *      This will be used as fallback alternative text if alternativeText
     *      is empty.
     *
     * @param [string]? $model->CSSClassNames
     *
     *      "hideSocial"
     *
     * @param object? (CBImage) $model->image
     * @param string? $model->size
     *
     *      The maximum width of the image in retina pixels. rw1600 (800pt) is
     *      the default.
     *
     *      rw320|rw640|rw960|rw1280|rw1600|rw1920|rw2560|original|page
     *
     * @return void
     */
    static function
    CBView_render(
        stdClass $model
    ): void
    {
        if (
            empty($model->image)
        ) {
            echo '<!-- CBArtworkView without an image. -->';

            return;
        }

        $renderImageOnly = CBModel::valueToBool(
            $model,
            'renderImageOnly'
        );

        $CSSClassNames = CBModel::valueToArray(
            $model,
            'CSSClassNames'
        );

        array_walk(
            $CSSClassNames,
            'CBHTMLOutput::requireClassName'
        );

        $CSSClassNames = cbhtml(
            implode(
                ' ',
                $CSSClassNames
            )
        );

        CBHTMLOutput::addPinterest();

        $imageModel = $model->image;

        $alternativeText = CBModel::valueToString(
            $model,
            'alternativeText'
        );


        /**
         * @NOTE 2017_06_26
         *
         * After working with customers and being annoyed with copying caption
         * into alternative text, I decided to make this imperfect change and
         * use the caption markdown as fallback alternitive text. It's more
         * imperfect if there's actual markdown formatting, but it still works.
         */

        if (
            empty($alternativeText)
        ) {
            $captionAsCBMessage = trim(
                CBModel::valueToString(
                    $model,
                    'captionAsCBMessage'
                )
            );

            $alternativeText = mb_substr(
                $captionAsCBMessage,
                0,
                100
            );
        }

        $captionAsHTML = CBModel::valueToString(
            $model,
            'captionAsHTML'
        );

        $size = CBModel::valueAsName(
            $model,
            'size'
        ) ?? 'rw1600';

        switch (
            $size
        ) {
            case 'rw320':

                $imageResizeOperation =
                $size;

                $maxWidth =
                160;

                break;

            case 'rw640':
                $imageResizeOperation =
                $size;

                $maxWidth =
                320;

                break;

            case 'rw960':

                $imageResizeOperation =
                $size;

                $maxWidth =
                480;

                break;

            case 'rw1280':

                $imageResizeOperation =
                $size;

                $maxWidth =
                640;

                break;

            case 'rw1920':

                $imageResizeOperation =
                $size;

                $maxWidth =
                960;

                break;

            case 'rw2560':

                $imageResizeOperation =
                $size;

                $maxWidth =
                1280;

                break;

            /**
             * @NOTE 2022_03_12
             *
             *      We won't actually show the original image because it may be
             *      too big or the file size may be too large. This option may
             *      be renamed at some point.
             *
             *      The user may theoretically want to display a small image at
             *      it's small original size and we won't do that either.
             */
            case 'original':

                $imageResizeOperation =
                'rw3200';

                $maxWidth =
                1600;

                break;

            case 'page':

                $imageResizeOperation =
                'rw3200';

                $maxWidth =
                10000;

                break;

            default:

                $imageResizeOperation =
                'rw1600';

                $maxWidth =
                800;

                break;
        }

        if (
            $maxWidth
        ) {
            $captionDeclarations =
            "max-width: {$maxWidth}px";
        } else {
            $captionDeclarations =
            '';
        }

        /**
         * @NOTE 2022_03_12
         *
         *      This view should actually have options for max height too, but
         *      for now it doesn't.
         */

        $maxHeight =
        10000;

        ?>

        <div class="CBArtworkView_root_element <?= $CSSClassNames ?>">

            <?php

            CBImage::renderPictureElementWithMaximumDisplayWidthAndHeight(
                $imageModel,
                $imageResizeOperation,
                $maxWidth,
                $maxHeight,
                $alternativeText
            );

            if (
                !$renderImageOnly &&
                !empty($captionAsHTML)
            ) {
                ?>

                <div
                    class="caption CBArtworkView_caption"
                    style="<?= $captionDeclarations ?>"
                >
                    <?= $captionAsHTML ?>
                </div>

                <?php
            }

            if (
                !$renderImageOnly
            ) {
                $pinterestImageURL = CBImage::asFlexpath(
                    $imageModel,
                    'rw1600',
                    cbsiteurl()
                );

                ?>

                <div
                    class="social CBArtworkView_socialMedia"
                    style="<?= $captionDeclarations ?>"
                >
                    <a href="https://www.pinterest.com/pin/create/button/"
                       data-pin-custom="true"
                       data-pin-description="<?= cbhtml($alternativeText) ?>"
                       data-pin-do="buttonPin"
                       data-pin-media="<?= $pinterestImageURL ?>">
                        Pin to Pinterest
                    </a>
                </div>

                <?php
            }

            ?>

        </div>

        <?php
    }
    /* CBView_render() */

}

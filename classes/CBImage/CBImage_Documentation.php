<?php

final class
CBImage_Documentation
{
    // -- functions



    /**
     * @return void
     */
    static function
    render(
    ): void
    {
        $CSS = <<<EOT

            main
            {
                --CBImage_Documentation_backgroundColor1:
                hsl(0, 0%, 97%);

                --CBImage_Documentation_backgroundColor2:
                hsl(0, 0%, 95%);
            }


            main
            img
            {
                background-image:
                linear-gradient(
                    45deg,

                    var(--CBImage_Documentation_backgroundColor1)
                    25%,

                    var(--CBImage_Documentation_backgroundColor2)
                    25%,

                    var(--CBImage_Documentation_backgroundColor2)
                    50%,

                    var(--CBImage_Documentation_backgroundColor1)
                    50%,

                    var(--CBImage_Documentation_backgroundColor1)
                    75%,

                    var(--CBImage_Documentation_backgroundColor2)
                    75%,

                    var(--CBImage_Documentation_backgroundColor2)
                    100%
                );

                background-size:
                14.14px
                14.14px;
            }

        EOT;

        CBHTMLOutput::addCSS(
            $CSS
        );

        $wideImageModel =
        CBModels::fetchModelByCBID(
            CB_SampleImages::getSampleImageModelCBID_5000x1000()
        );

        $narrowImageModel =
        CBModels::fetchModelByCBID(
            CB_SampleImages::getSampleImageModelCBID_1000x5000()
        );

        CBImage_Documentation::renderCBMessage(
            <<<EOT

            CBImage::renderPictureElementWithImageInsideAspectRatioBox()

            Narrow Image

            EOT
        );

        CBImage::renderPictureElementWithImageInsideAspectRatioBox(
            $narrowImageModel,
            'rl1280',
            480,
            480
        );

        CBImage_Documentation::renderCBMessage(
            <<<EOT

            CBImage::renderPictureElementWithImageInsideAspectRatioBox()

            Wide Image

            EOT
        );

        CBImage::renderPictureElementWithImageInsideAspectRatioBox(
            $wideImageModel,
            'rl1280',
            480,
            480
        );



        CBImage_Documentation::renderCBMessage(
            <<<EOT

            CBImage::renderPictureElementWithMaximumDisplayWidthAndHeight()

            Narrow Image

            EOT
        );

        CBImage::renderPictureElementWithMaximumDisplayWidthAndHeight(
            $narrowImageModel,
            'rl1280',
            480,
            480
        );

        CBImage_Documentation::renderCBMessage(
            <<<EOT

            CBImage::renderPictureElementWithMaximumDisplayWidthAndHeight()

            Wide Image

            EOT
        );

        CBImage::renderPictureElementWithMaximumDisplayWidthAndHeight(
            $wideImageModel,
            'rl1280',
            480,
            480
        );
    }
    // render()



    /**
     * @param string $cbmessage
     *
     * @return void
     */
    static function
    renderCBMessage(
        string $cbmessage
    ): void
    {
        $messageViewSpec =
        CBModel::createSpec(
            'CBMessageView'
        );

        CBMessageView::setCBMessage(
            $messageViewSpec,
            $cbmessage
        );

        CBView::renderSpec(
            $messageViewSpec
        );
    }
    // renderCBMessage()

}

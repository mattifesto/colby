<?php

final class CBArtworkElement {

    /* -- CBHTMLOutput interfaces -- -- -- -- -- */



    /**
     * @return [string]
     */
    static function CBHTMLOutput_CSSURLs() {
        return [
            Colby::flexpath(__CLASS__, 'v618.css', cbsysurl()),
        ];
    }



    /**
     * @return [string]
     */
    static function CBHTMLOutput_JavaScriptURLs() {
        return [
            Colby::flexpath(__CLASS__, 'v624.js', cbsysurl()),
        ];
    }



    /**
     * @return [string]
     */
    static function CBHTMLOutput_requiredClassNames() {
        return [
            'CBModel',
        ];
    }



    /* -- functions -- -- -- -- -- */



    /**
     * @param ?float $maxWidth
     * @param ?float $maxHeight
     * @param float $aspectRatioWidth
     * @param float $aspectRatioHeight
     *
     * @return ?float
     */
    static function calculateMaxWidth(
        ?float $maxWidth,
        ?float $maxHeight,
        float $aspectRatioWidth,
        float $aspectRatioHeight
    ): ?float {
        $result = null;

        if ($maxHeight !== null) {
            $result = $maxHeight * ($aspectRatioWidth / $aspectRatioHeight);

            if ($maxWidth !== null) {
                $result = min($result, $maxWidth);
            }
        } else if ($maxWidth !== null) {
            $result = $maxWidth;
        }

        return $result;
    }
    /* calculateMaxWidth() */



    /**
     * @param object|array $args
     *
     *      @TODO 2021_01_11
     *
     *          Passing arguments in an array is deprecated. We may want to
     *          deprecate this function and replace it with render2() to enforce
     *          that change. Also consider switching to named arguments, a
     *          feature that only works in PHP >= 8.
     *
     *      {
     *          URL: string
     *
     *              The URL for the image.
     *
     *              @TODO 2021_01_11
     *
     *                  If the image URL is empty we render an img element with
     *                  no URL which is a bit odd. Determine and document the
     *                  desired behavior for an empty image URL.
     *
     *                  For now, if you don't want an odd img element rendered,
     *                  check for an empty image URL before calling this
     *                  function and choose your own alternative action.
     *
     *          aspectRatioWidth: number (default: 1)
     *          aspectRatioHeight: number (default: 1)
     *          width: number (deprecated, use aspectRatioWidth)
     *          height: number (deprecated, use aspectRatioHeight)
     *
     *              These arguments specify the aspect ratio of the container.
     *              Callers often provide the original image dimensions in
     *              pixels, because that is what is available in the CBImage
     *              model. Other times, such as when fitting the image in a
     *              square aspect ratio container, they may be 1 and 1.
     *
     *          maxWidth: number (optional)
     *          maxHeight: number (optional)
     *
     *              The units for these properties are CSS pixels.
     *
     *          alternativeText: string
     *      }
     *
     * @return void
     */
    static function render(
        $args
    ): void {
        CBHTMLOutput::requireClassName(
            'CBArtworkElement'
        );

        if (is_array($args)) {
            $args = (object)$args;
        } else {
            $args = CBConvert::valueToObject($args);
        }

        $URL = CBModel::valueToString($args, 'URL');

        $aspectRatioWidth = (
            CBModel::valueAsNumber($args, 'aspectRatioWidth') ??
            CBModel::valueAsNumber($args, 'width') ?? /* deprecated */
            1
        );

        $aspectRatioHeight = (
            CBModel::valueAsNumber($args, 'aspectRatioHeight') ??
            CBModel::valueAsNumber($args, 'height') ?? /* deprecated */
            1
        );

        $maxWidth = CBModel::valueAsNumber($args, 'maxWidth');
        $maxHeight = CBModel::valueAsNumber($args, 'maxHeight');
        $alternativeText = CBModel::valueToString($args, 'alternativeText');

        $calculatedMaxWidth = CBArtworkElement::calculateMaxWidth(
            $maxWidth,
            $maxHeight,
            $aspectRatioWidth,
            $aspectRatioHeight
        );

        $ID = CBID::generateRandomCBID();

        if ($calculatedMaxWidth !== null) {
            $CSSWidth = "{$calculatedMaxWidth}px";
        } else {
            $CSSWidth = '100vw';
        }

        {
            $ratio = $aspectRatioHeight / $aspectRatioWidth;
            $CSSPaddingBottom = $ratio * 100;
            $CSSPaddingBottom = "{$CSSPaddingBottom}%";
        }

        $styleSheet = <<<EOT

            /* CBArtworkElement */

            .ID_{$ID} {
                width: {$CSSWidth};
            }

            .ID_{$ID} > div {
                padding-bottom: {$CSSPaddingBottom};
            }

        EOT;

        CBHTMLOutput::addCSS($styleSheet);

        ?>

        <div class="CBArtworkElement ID_<?= $ID ?>">
            <div>
                <img
                    src="<?= cbhtml($URL) ?>"
                    alt="<?= cbhtml($alternativeText) ?>"
                >
            </div>
        </div>

        <?php
    }
    /* render() */

}

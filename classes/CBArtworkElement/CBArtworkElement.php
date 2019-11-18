<?php

final class CBArtworkElement {

    /**
     * @return [string]
     */
    static function CBHTMLOutput_CSSURLs() {
        return [
            Colby::flexpath(__CLASS__, 'v383.css', cbsysurl()),
        ];
    }

    /**
     * @return [string]
     */
    static function CBHTMLOutput_JavaScriptURLs() {
        return [
            Colby::flexpath(__CLASS__, 'v453.js', cbsysurl()),
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

    /**
     * @param object|array $args
     *
     *      passing arguments in an array is deprecated
     *
     *      {
     *          URL: string
     *
     *              The URL for the image.
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
    static function render($args): void {
        CBHTMLOutput::requireClassName('CBArtworkElement');

        if (is_array($args)) {
            $args = (object)$args;
        } else {
            $args = CBConvert::valueToObject($args);
        }

        $URL = CBModel::valueToString($args, 'URL');
        $aspectRatioWidth = CBModel::valueAsNumber($args, 'aspectRatioWidth') ??
                            CBModel::valueAsNumber($args, 'width') ?? /* deprecated */
                            1;
        $aspectRatioHeight = CBModel::valueAsNumber($args, 'aspectRatioHeight') ??
                             CBModel::valueAsNumber($args, 'height') ?? /* deprecated */
                             1;
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
                <img src="<?= cbhtml($URL) ?>" alt="<?= cbhtml($alternativeText) ?>">
            </div>
        </div>

        <?php
    }
}

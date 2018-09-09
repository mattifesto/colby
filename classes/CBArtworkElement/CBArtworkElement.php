<?php

/**
 * The purpose of CBArtworkElement is to be an easy to use alternative to the
 * image element that has a constant layout regardless of whether the image has
 * loaded.
 *
 * The CBArtworkElement may grow or shrink in responsive design scenarios.
 *
 * The primary difference between CBArtworkElement and the img element is that a
 * CBArtworkElement requires an aspect ratio to be specified. Usually this is
 * aspect ratio of the image. The aspect ratio may be different than that of the
 * image, in which case the image will be fit and centered inside an aspect
 * ratio shaped container.
 *
 *      Example: In scenarios where many images are shown adjacent to each
 *      other, such as a list of products where the product images may have
 *      various aspect ratios, rendering CBArtworkElements with a square aspect
 *      ratio will normalize the layout.
 *
 * A CBArtworkElement completely detaches an image's actual size in pixels from
 * the layout process. Specify images with a pixel size to provide the desired
 * detail level for the expected viewing sizes you expect the CBArtworkElement
 * to have.
 *
 *      Example: If you know your CBArtworkElement is going to be viewed at
 *      around 320 CSS pixels wide, you will probably want to specify an image
 *      file that has 640 actual pixels of width. You may want to specify an
 *      even larger image file if you expect them image to be zoomed often.
 *
 * @NOTE
 *
 *      A CBImage model holds an image's original size, and this size can be
 *      used to specify the aspect ratio for a CBArtworkElement.
 *
 *      In older browsers that don't support object-fit, most notably IE 11,
 *      images may overflow the container and be cropped. Colby treats IE 11 as
 *      a pseudo-supported browser where everything must technically work, but
 *      imperfections are accepted because the browser is basically deprecated.
 *      In this case, CBArtworkElement consideres the constant layout to be more
 *      important than displaying the entire image.
 */
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
     * @param int $maxHeight
     *
     *      The length in any units of the maximum desired height.
     *
     * @param int $aspectWidth
     *
     *      The length in any units (same as $aspectHeight) of the image width.
     *
     * @param int $aspectHeight
     *
     *      The length in any units (same as $aspectWidth) of the image height.
     *
     * @return int
     */
    static function maxHeightToMaxWidth($maxHeight, $aspectWidth, $aspectHeight) {
        return $maxHeight * ($aspectWidth / $aspectHeight);
    }

    /**
     * @NOTE:
     *
     * @param string $args['URL']
     *
     *      The URL for the image. The size of this image has no effect on the
     *      layout of the CBArtworkElement. The recommended image size is two
     *      times the number of CSS pixels of the "usual" viewing size of the
     *      CBArtworkElement. So if you expect the CBArtworkElement will usually
     *      be seen as 800px by 400px wide use an image with 1600 by 800 actual
     *      pixels.
     *
     * @param float $args['width']
     * @param float $args['height']
     *
     *      These arguments specify the aspect ratio of the container. They are
     *      often the original image dimensions in pixels, because that is what
     *      is available in the CBImage model. Other times, such as when
     *      specifying a square aspect ratio, they may be 1 and 1.
     *
     * @param float $args['maxWidth'] (optional)
     * @param float $args['maxHeight'] (optional)
     *
     *      The maximum width and/or height in CSS pixels that the image should
     *      be displayed. CBArtworkElement images always expand to fit the
     *      available width. These properties place limits on that expansion.
     *
     * @param string $args['alternativeText'] (optional)
     *
     *      The alternative text for the image.
     *
     * @return void
     */
    static function render(array $args = []): void {
        if (empty($args['height']) || empty($args['width']) || empty($args['URL'])) {
            return;
        }

        CBHTMLOutput::requireClassName('CBArtworkElement');

        $aspectWidth = $args['width'];
        $aspectHeight = $args['height'];
        $ID = CBHex160::random();
        $inverseAspectRatio = $aspectHeight / $aspectWidth;
        $URLAsHTML = cbhtml($args['URL']);
        $paddingBottom = $inverseAspectRatio * 100;
        $paddingBottomDeclaration = "padding-bottom: {$paddingBottom}%";

        $maxWidth = false;

        if (!empty($args['maxHeight'])) {
            $maxWidth = CBArtworkElement::maxHeightToMaxWidth($args['maxHeight'], $aspectWidth, $aspectHeight);
        }

        if (!empty($args['maxWidth'])) {
            if ($maxWidth) {
                $maxWidth = min(floatval($args['maxWidth']), $maxWidth);
            } else {
                $maxWidth = floatval($args['maxWidth']);
            }
        }

        if ($maxWidth) {
            $widthDeclaration = "width: {$maxWidth}px";
        } else {
            $widthDeclaration = 'width: 100vw';
        }

        if (empty($args['alternativeText'])) {
            $alternativeTextAsHTML = '';
        } else {
            $alternativeTextAsHTML = cbhtml($args['alternativeText']);
        }

        $styleSheet = <<<EOT

/* CBArtworkElement */

.ID_{$ID} {
    {$widthDeclaration};
}

.ID_{$ID} > div {
    {$paddingBottomDeclaration};
}

EOT;

        CBHTMLOutput::addCSS($styleSheet);

        ?>

        <div class="CBArtworkElement ID_<?= $ID ?>">
            <div>
                <img src="<?= $URLAsHTML ?>" alt="<?= $alternativeTextAsHTML ?>">
            </div>
        </div>

        <?php
    }
}

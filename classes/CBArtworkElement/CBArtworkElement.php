<?php

/**
 * The CBArtworkElement represents an element of an image that may have an
 * optional maximum width and will shrink fit its container width if necessary.
 * Its inherent resizability is what differentiates it from the <img> element.
 *
 * Because the implementation may change you should avoid styling element.
 * Acceptable styles:
 *  - Add a frame with a border style.
 */
final class CBArtworkElement {

    /**
     * @param int $maxHeight
     *  The length in any units of the maximum desired height.
     * @param int $aspectWidth
     *  The length in any units (same as $aspectHeight) of the image width.
     * @param int $aspectHeight
     *  The length in any units (same as $aspectWidth) of the image height.
     * @return int
     */
    static function maxHeightToMaxWidth($maxHeight, $aspectWidth, $aspectHeight) {
        return $maxHeight * ($aspectWidth / $aspectHeight);
    }

    /**
     * @param string? $args['alternativeText']
     *  The alternative text for the image.
     * @param int $args['height']
     *  The aspect height of the image, image pixel height can work here.
     * @param int $args['width']
     *  The aspect width of the image, image pixel width can work here.
     * @param float? $args['maxHeight']
     *  The maximum height in CSS pixels that the image should be displayed.
     * @param float? $args['maxWidth']
     *  The maximum width in CSS pixels that the image should be displayed.
     * @param string $args['URL']
     *  The URL for the image.
     *
     * @return null
     */
    static function render(array $args = []) {
        if (empty($args['height']) || empty($args['width']) || empty($args['URL'])) {
            return;
        }

        $aspectWidth = $args['width'];
        $aspectHeight = $args['height'];
        $ID = CBHex160::random();
        $inverseAspectRatio = $aspectHeight/ $aspectWidth;
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

        /**
         * NOTE: It's important the style of this element be in the format:
         *
         *      width: 640px; max-width: 100%;
         *
         *  instead of:
         *
         *      width: 100%; max-width: 640px;
         *
         *  In most scenarios, these two formats mean the same thing. But not
         *  when the element is a child of a `display: flex` parent. In this
         *  case the first format expresses a desired size and the element will
         *  have that size unless it won't fit and then it will be allowed to
         *  shrink. The second format will allow the element to be reduced to
         *  zero width by its parent.
         */

        ?>

        <div class="CBArtworkElement ID-<?= $ID ?>">
            <style>
                <?= ".ID-{$ID}" ?> {
                    <?= $widthDeclaration ?>;
                    max-width: 100%;
                }

                <?= ".ID-{$ID}" ?> > div {
                    overflow: hidden;
                    position: relative;
                    <?= $paddingBottomDeclaration ?>;
                }

                <?= ".ID-{$ID}" ?> > div > img {
                    left: 0;
                    position: absolute;
                    top: 0;
                    width: 100%;
                }
            </style>
            <div>
                <img src="<?= $URLAsHTML ?>" alt="<?= $alternativeTextAsHTML ?>">
            </div>
        </div>

        <?php
    }

    /**
     * @return [string]
     */
    static function requiredJavaScriptURLs() {
        return [Colby::flexnameForJavaScriptForClass(CBSystemURL, __CLASS__)];
    }
}

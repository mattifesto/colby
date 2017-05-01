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
     * @param string? $args['alternativeText']
     *  The alternative text for the image.
     * @param int $args['height']
     *  The aspect height of the image, image pixel height can work here.
     * @param int $args['width']
     *  The aspect width of the image, image pixel width can work here.
     * @param float? $args['maxWidth']
     *  The maximum with in CSS pixels that the image should be displayed.
     * @param string $args['URL']
     *  The URL for the image.
     *
     * @return null
     */
    static function render(array $args = []) {
        if (empty($args['height']) || empty($args['width']) || empty($args['URL'])) {
            return;
        }

        $ID = CBHex160::random();
        $inverseAspectRatio = $args['height'] / $args['width'];
        $URLAsHTML = cbhtml($args['URL']);
        $paddingBottom = $inverseAspectRatio * 100;
        $paddingBottom = "padding-bottom: {$paddingBottom}%;";

        if (empty($args['maxWidth'])) {
            $maxWidth = '/* no max-width */';
        } else {
            $maxWidth = floatval($args['maxWidth']);
            $maxWidth = "max-width: {$maxWidth}px;";
        }

        if (empty($args['alternativeText'])) {
            $alternativeTextAsHTML = '';
        } else {
            $alternativeTextAsHTML = cbhtml($args['alternativeText']);
        }

        ?>

        <div class="CBArtworkElement ID-<?= $ID ?>">
            <style>
                .ID-<?= $ID ?> {
                    <?= $maxWidth ?>
                    width: 100%;
                }

                .ID-<?= $ID ?> > div {
                    overflow: hidden;
                    position: relative;
                    <?= $paddingBottom ?>
                }

                .ID-<?= $ID ?> > div > img {
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

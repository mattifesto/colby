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
     * @NOTE: 2017.07.16 This class does not have a CBView_render() function
     *        because it is not meant to be used with a model. It is just
     *        supposed to be a set of functions. However, it does have a
     *        required style sheet so it adds itself as a required class name.
     *
     *        The JavaScript is not required by a call to this function, but it
     *        feels cleaner to have it as a requirement of the class rather than
     *        using a different method of inclusion.
     *
     * @param string? $args['alternativeText']
     *
     *      The alternative text for the image.
     *
     * @param int $args['height']
     *
     *      The aspect height of the image, image pixel height can work here.
     *
     * @param int $args['width']
     *
     *      The aspect width of the image, image pixel width can work here.
     *
     * @param float? $args['maxHeight']
     *
     *      The maximum height in CSS pixels that the image should be displayed.
     *
     * @param float? $args['maxWidth']
     *
     *      The maximum width in CSS pixels that the image should be displayed.
     *
     * @param string $args['URL']
     *
     *      The URL for the image.
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

    /**
     * @return [string]
     */
    static function CBHTMLOutput_CSSURLs() {
        return [Colby::flexpath(__CLASS__, 'v383.css', cbsysurl())];
    }

    /**
     * @return [string]
     */
    static function CBHTMLOutput_JavaScriptURLs() {
        return [Colby::flexpath(__CLASS__, 'v383.js', cbsysurl())];
    }
}

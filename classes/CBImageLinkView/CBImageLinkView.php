<?php

/**
 * @TODO 2019_06_16
 *
 *      This view needs investigation and should probably be deprecated. The
 *      description below was written before the artwork element was developed.
 *
 * This class is meant to a simple and universally useful view to display an
 * image with an optional link. Part of the simplicity of this class is that it
 * does not allow for image resizing. Image resizing usually requires domain
 * specific knowledge and is not a great feature for a view that is meant to be
 * useful everywhere.
 *
 * Two features, aside from image uploading and display, have been included to
 * make this class useful for the long term. The first is to allow an optional
 * link which is needed in the majority of scenarios. The other is to allow the
 * image to be specified as "retina" which will allow the view to be useful into
 * the foreseeable future. Declaring the image as "retina" will use the same
 * image but will render it at half size.
 */
final class CBImageLinkView {

    /* -- CBHTMLOutput interfaces -- -- -- -- -- */

    /**
     * @return [string]
     */
    static function CBHTMLOutput_CSSURLs() {
        return [
            Colby::flexpath(__CLASS__, 'v485.css', cbsysurl())
        ];
    }


    /* -- CBModel interfaces -- -- -- -- -- */

    /**
     * @param model $spec
     *
     *      {
     *          alt: string
     *
     *              The alternative text for the image
     *
     *          density: string
     *
     *              The pixel density of the image ('1x'|'2x')
     *
     *          height: int
     *
     *              The height of the image in pixels
     *
     *          image: object
     *
     *              The image spec for the image used.
     *
     *          HREF: string
     *
     *              The link HREF
     *
     *          URL: string
     *
     *              The image URL
     *
     *          width: int
     *
     *              The width of the image in pixels
     *      }
     *
     * @return object
     */
    static function CBModel_build(stdClass $spec): stdClass {
        $model = (object)[
            'alt' => CBModel::valueToString($spec, 'alt'),
            'retina' => !empty($spec->retina),
            'density' => empty($spec->retina) ? '1x' : '2x',
            'height' => CBModel::valueAsInt($spec, 'height'),
            'HREF' => CBModel::valueToString($spec, 'HREF'),
            'URL' => CBModel::valueToString($spec, 'URL'),
            'width' => CBModel::valueAsInt($spec, 'width'),
        ];

        /* image */

        if ($imageSpec = CBModel::valueAsModel($spec, 'image', ['CBImage'])) {
            $model->image = CBModel::build($imageSpec);
        }

        return $model;
    }
    /* CBModel_build() */


    /**
     * @param model $model
     *
     * @return string
     */
    static function CBModel_toSearchText(stdClass $model): string {
        return CBModel::valueToString($model, 'alt');
    }


    /* -- CBView interfaces -- -- -- -- -- */

    /**
     * @param model $model
     *
     * @return void
     */
    static function CBView_render(stdClass $model): void {
        $height = CBModel::valueAsInt($model, 'height');
        $width = CBModel::valueAsInt($model, 'width');

        if ($height === null || $width === null) {
            return;
        }

        $density = CBModel::valueToString($model, 'density');

        switch ($density) {
            case '2x':
                $height = ceil($height / 2);
                $width = ceil($width / 2);
                break;

            default:
                break;
        }

        $styles = "height: {$height}px; width: {$width}px;";

        if ($HREF = CBModel::valueToString($model, 'HREF')) {
            $tag = 'a';
            $href = 'href="' . cbhtml($HREF) . '"';
        } else {
            $tag = 'div';
            $href = '';
        }

        $URL = CBModel::valueToString($model, 'URL');
        $alt = CBModel::valueToString($model, 'alt');

        ?>

        <div class="CBImageLinkView">
            <<?= $tag ?> class="CBImageLinkView_container" <?= $href ?>>
                <img
                    src="<?= cbhtml($URL) ?>"
                    alt="<?= cbhtml($alt) ?>"
                    style="<?= $styles ?>"
                >
            </<?= $tag ?>>
        </div>

        <?php
    }
    /* CBView_render() */
}
/* CBImageLinkView */

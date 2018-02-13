<?php

/**
 *     This class is meant to a simple and universally useful view to display
 * an image with an optional link. Part of the simplicity of this class is that
 * it does not allow for image resizing. Image resizing usually requires
 * domain specific knowledge and is not a great feature for a view that is
 * meant to be useful everywhere.
 *     Two features, aside from image uploading and display, have been included
 * to make this class useful for the long term. The first is to allow an
 * optional link which is needed in the majority of scenarios. The other is to
 * allow the image to be specified as "retina" which will allow the view to be
 * useful into the foreseeable future. Declaring the image as "retina" will use
 * the same image but will render it at half size.
 */
final class CBImageLinkView {

    /**
     * @return [string]
     */
    static function CBHTMLOutput_CSSURLs() {
        return [Colby::flexpath(__CLASS__, 'css', cbsysurl())];
    }

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
     * @return ?model
     */
    static function CBModel_build(stdClass $spec): ?stdClass {
        $model = (object)[];

        $model->alt = isset($spec->alt) ? (string)$spec->alt : null;
        $model->altAsHTML = ColbyConvert::textToHTML($model->alt);
        $retina = CBModel::value($spec, 'retina', false);
        $model->density = $retina ? '2x' : '1x';
        $model->height = isset($spec->height) ? (int)$spec->height : null;
        $model->HREF = CBModel::value($spec, 'HREF', null, 'trim');
        $model->HREFAsHTML = cbhtml($model->HREF);
        $model->URL = isset($spec->URL) ? (string)$spec->URL : null;
        $model->URLAsHTML = ColbyConvert::textToHTML($model->URL);
        $model->width = isset($spec->width) ? (int)$spec->width : null;

        return $model;
    }

    /**
     * @param model $model
     *
     * @return string
     */
    static function CBModel_toSearchText(stdClass $model): string {
        return CBModel::valueToString($model, 'alt');
    }

    /**
     * @param object $model
     *
     * @return null
     */
    static function CBView_render(stdClass $model) {
        switch ($model->density) {
            case '2x':
                $height = ceil($model->height / 2);
                $width = ceil($model->width / 2);
                break;

            default:
                $height = $model->height;
                $width = $model->width;
                break;
        }

        $styles = "height: {$height}px; width: {$width}px;";

        if (empty($model->HREF)) {
            $tag = 'div';
            $href = '';
        } else {
            $tag = 'a';
            $href = 'href="' . $model->HREF . '"';
        }

        ?>

        <<?= $tag ?> class="CBImageLinkView" <?= $href ?>>
            <img src="<?= $model->URLAsHTML ?>" alt="<?= $model->altAsHTML ?>" style="<?= $styles ?>">
        </<?= $tag ?>>

        <?php
    }
}

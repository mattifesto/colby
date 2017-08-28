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
     * @return {string}|null
     */
    public static function modelToSearchText(stdClass $model) {
        return $model->alt;
    }

    /**
     * @return null
     */
    public static function CBView_render(stdClass $model) {
        CBHTMLOutput::addCSSURL(self::URL('CBImageLinkViewHTML.css'));

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

    /**
     * Spec properties:
     *  {string}    alt     The alternative text for the image
     *  {string}    density The pixel density of the image ('1x'|'2x')
     *  {int}       height  The height of the image in pixels
     *  {string}    HREF    The link HREF
     *  {string}    URL     The image URL
     *  {int}       width   The width of the image in pixels
     *
     * @return {stdClass}
     */
    public static function specToModel(stdClass $spec) {
        $model = (object)['className' => __CLASS__];
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
     * @param string $filename
     *
     * @return string
     */
    public static function URL($filename) {
        $className = __CLASS__;
        return CBSystemURL . "/classes/{$className}/{$filename}";
    }
}

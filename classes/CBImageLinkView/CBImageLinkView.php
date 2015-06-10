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
     * @return null
     */
    public static function includeEditorDependencies() {
        CBHTMLOutput::addCSSURL(self::URL('CBImageLinkViewEditor.css'));
        CBHTMLOutput::addJavaScriptURL(self::URL('CBImageLinkViewEditorFactory.js'));
    }

    /**
     * @return {string}|null
     */
    public static function modelToSearchText(stdClass $model) {
        return $model->alt;
    }

    /**
     * @return null
     */
    public static function renderModelAsHTML(stdClass $model) {
        CBHTMLOutput::addCSSURL(self::URL('CBImageLinkViewHTML.css'));

        echo '<div class="CBImageLinkView">';

        if ($model->HREF) {
            echo "<a href=\"{$model->HREF}\">";
        }

        switch ($model->density) {
            case '2x':
                $height = ceil($model->height / 2);
                $width  = ceil($model->width / 2);
                break;

            default:
                $height = $model->height;
                $width  = $model->width;
                break;
        }

        $CSS = "width: {$width}px; height: {$height}px;";

        echo "<img src=\"{$model->URLAsHTML}\" alt=\"{$model->altAsHTML}\" style=\"{$CSS}\">";

        if ($model->HREF) {
            echo '</a>';
        }

        echo '</div>';
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
        $model              = CBView::modelWithClassName(__CLASS__);
        $model->alt         = isset($spec->alt) ? (string)$spec->alt : null;
        $model->altAsHTML   = ColbyConvert::textToHTML($model->alt);
        $model->density     = isset($spec->density) ? (string)$spec->density : '1x';
        $model->height      = isset($spec->height) ? (int)$spec->height : null;
        $model->HREF        = isset($spec->HREF) ? (string)$spec->HREF : null;
        $model->HREFAsHTML  = ColbyConvert::textToHTML($model->HREF);
        $model->URL         = isset($spec->URL) ? (string)$spec->URL : null;
        $model->URLAsHTML   = ColbyConvert::textToHTML($model->URL);
        $model->width       = isset($spec->width) ? (int)$spec->width : null;

        return $model;
    }

    public static function URL($filename) {
        return CBSystemURL . "/classes/CBImageLinkView/{$filename}";
    }
}

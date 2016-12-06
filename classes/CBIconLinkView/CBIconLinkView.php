<?php

final class CBIconLinkView {

    /**
     * @param string? $model->URLAsHTML
     * @param string? $model->textColor
     *
     * @return [string:string]
     */
    private static function containerElement(stdClass $model) {
        if (empty($model->URLAsHTML)) {
            $hrefAttribute = '';
            $tagName = 'div';
        } else {
            $hrefAttribute = "href=\"{$model->URLAsHTML}\"";
            $tagName = 'a';
        }

        if (empty($model->textColor)) {
            $styleAttribute = '';
        } else {
            $styleAttribute = "style=\"color: {$model->textColor}\"";
        }

        return [
            'openHTML' => "<{$tagName} class=\"container\" {$hrefAttribute} {$styleAttribute}>",
            'closeHTML' => "</{$tagName}>",
        ];
    }

    /**
     * @param bool? $model->disableRoundedCorners
     * @param stdClass? $model->image
     *
     * @return string
     */
    private static function imageElementHTML(stdClass $model) {
        if (empty($model->image)) {
            $imageCSS = 'background-color: hsl(30, 50%, 80%)';
        } else {
            $imageCSS = CBImage::flexpath($model->image);
            $imageCSS = "background-image: url(/{$imageCSS});";
        }

        if (empty($model->disableRoundedCorners)) {
            $classes = "icon rounded";
        } else {
            $classes = "icon";
        }

        return "<div class=\"{$classes}\" style=\"{$imageCSS}\"></div>";
    }

    /**
     * @param string? $model->textAsHTML
     *
     * @return string
     */
    private static function textElementHTML(stdClass $model) {
        if (empty($model->textAsHTML)) {
            return '';
        } else {
            return "<div class=\"text\">{$model->textAsHTML}</div>";
        }
    }

    /**
     * @param string? $model->textAsHTML
     * @param string? $model->URLAsHTML
     * @param stdClass? $model->image
     *
     * @return null
     */
    public static function renderModelAsHTML(stdClass $model) {
        CBHTMLOutput::requireClassName(__CLASS__);

        $containerElement = CBIconLinkView::containerElement($model);
        $imageElementHTML = CBIconLinkView::imageElementHTML($model);
        $textElementHTML = CBIconLinkView::textElementHTML($model);

        ?>

        <div class="CBIconLinkView">
            <?= $containerElement['openHTML'] ?>
                <?= $imageElementHTML ?>
                <?= $textElementHTML ?>
            <?= $containerElement['closeHTML'] ?>
        </div>

    <?php }

    /**
     * @return [string]
     */
    public static function requiredCSSURLs() {
        return [Colby::flexnameForCSSForClass(CBSystemURL, __CLASS__)];
    }

    /**
     * @param stdClass $spec
     *
     * @return stdClass
     */
    public static function specToModel(stdClass $spec) {
        return (object)[
            'className' => __CLASS__,
            'alternativeText' => CBModel::value($spec, 'alternativeText', 'strval'),
            'disableRoundedCorners' => CBModel::value($spec, 'disableRoundedCorners', false, 'boolval'),
            'image' => CBModel::value($spec, 'image', null, 'CBImage::specToModel'),
            'text' => ($text = CBModel::value($spec, 'text', '', 'trim')),
            'textAsHTML' => cbhtml($text),
            'textColor' => CBModel::value($spec, 'textColor', null, 'CBConvert::stringToCSSColor'),
            'URL' => ($URL = CBModel::value($spec, 'URL', '', 'trim')),
            'URLAsHTML' => cbhtml($URL),
        ];
    }
}

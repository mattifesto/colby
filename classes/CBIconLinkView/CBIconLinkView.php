<?php

final class CBIconLinkView {

    /**
     * @param object $model
     *
     * @return string
     */
    static function CBModel_toSearchText(stdClass $model) {
        $strings[] = CBModel::value($model, 'text');
        $strings[] = CBModel::value($model, 'alternativeText');

        return implode(' ', array_filter($strings));
    }

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
     * @param object? $model->image
     *
     * @return string
     */
    private static function imageElementHTML(stdClass $model) {
        if ($flexpath = CBImage::valueToFlexpath($model, 'image', 'rw640')) {
            $imageCSS = "background-image: url(/{$flexpath});";
        } else {
            $imageCSS = 'background-color: hsl(30, 50%, 80%)';
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
    static function CBView_render(stdClass $model) {
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
    static function requiredCSSURLs() {
        return [Colby::flexnameForCSSForClass(CBSystemURL, __CLASS__)];
    }

    /**
     * @param object $spec
     *
     * @return object
     */
    static function CBModel_toModel(stdClass $spec) {
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

<?php

final class CBIconLinkView {

    /**
     * @param stdClass $model
     *
     * @return null
     */
    public static function renderModelAsHTML(stdClass $model) {
        CBHTMLOutput::requireClassName(__CLASS__);

        $textAsHTML = empty($model->textAsHTML) ? '' : $model->textAsHTML;
        $HREF = empty($model->URLAsHTML) ? '' : "href=\"{$model->URLAsHTML}\"";

        if (empty($model->textAsHTML)) {
            $textElement = '';
        } else {
            $textElement = "<div class=\"text\">{$model->textAsHTML}</div>";
        }

        if (empty($model->image)) {
            $imageCSS = 'background-color: hsl(30, 50%, 80%)';
        } else {
            $imageCSS = CBImage::flexpath($model->image);
            $imageCSS = "background-image: url(/{$imageCSS}); background-size: cover";
        }

        $style = empty($model->textColor) ? '' : " style=\"color: {$model->textColor}\"";

        ?>

        <div class="CBIconLinkView">
            <a class="container" <?= $HREF, $style ?>>
                <div class="icon" style="<?= $imageCSS ?>"></div>
                <?= $textElement?>
            </a>
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
            'image' => CBModel::value($spec, 'image', null, 'CBImage::specToModel'),
            'text' => ($text = CBModel::value($spec, 'text', '', 'trim')),
            'textAsHTML' => cbhtml($text),
            'textColor' => CBModel::value($spec, 'textColor', null, 'CBConvert::stringToCSSColor'),
            'URL' => ($URL = CBModel::value($spec, 'URL', '', 'trim')),
            'URLAsHTML' => cbhtml($URL),
        ];
    }
}

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

        ?>

        <div class="CBIconLinkView">
            <a class="container" <?= $HREF ?>>
                <div class="icon"></div>
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
            'text' => ($text = CBModel::value($spec, 'text', '', 'trim')),
            'textAsHTML' => cbhtml($text),
            'URL' => ($URL = CBModel::value($spec, 'URL', '', 'trim')),
            'URLAsHTML' => cbhtml($URL),
        ];
    }
}

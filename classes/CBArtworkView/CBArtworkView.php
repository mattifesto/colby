<?php

final class CBArtworkView {

    /**
     * @param string? $model->alternativeText
     *
     * @return null
     */
    static function renderModelAsHTML(stdClass $model) {
        if (empty($model->image)) {
            echo '<!-- CBArtworkView without an image. -->';
            return;
        }

        CBHTMLOutput::requireClassName(__CLASS__);
        CBHTMLOutput::addPinterest();

        echo '<figure class="CBArtworkView">';

        $image = $model->image;
        $basename = "rw1600.{$image->extension}";
        $imageURL = CBDataStore::flexpath($image->ID, $basename, CBSiteURL);
        $alternativeText = CBModel::value($model, 'alternativeText', '');
        $alternativeTextAsHTML = cbhtml($alternativeText);
        $captionAsHTML = CBModel::value($model, 'captionAsHTML', '', 'trim');

        CBArtworkElement::render([
            'alternativeText' => $alternativeText,
            'height' => $image->height,
            'maxWidth' => 800,
            'width' => $image->width,
            'URL' => $imageURL,
        ]);

        if (!empty($captionAsHTML)) { ?>
            <div class="caption">
                <?= $captionAsHTML ?>
            </div>
        <?php }

        ?>

        <div class="pin">
            <a href="https://www.pinterest.com/pin/create/button/"
               data-pin-custom="true"
               data-pin-description="<?= $alternativeTextAsHTML ?>"
               data-pin-do="buttonPin"
               data-pin-media="<?= $imageURL ?>">
                Pin to Pinterest
            </a>
        </div>

        <?php

        echo '</figure>';
    }

    /**
     * @return [string]
     */
    static function requiredCSSURLs() {
        return [Colby::flexnameForCSSForClass(CBSystemURL, __CLASS__)];
    }

    /**
     * @param string? $spec->alternativeText
     * @param string $spec->image?->extension
     * @param string $spec->image?->filename
     * @param int $spec->image?->height
     * @param hex160 $spec->image?->ID
     * @param int $spec->image?->width
     *
     * @return stdClass
     */
    static function specToModel(stdClass $spec) {
        $model = (object)[
            'className' => __CLASS__,
            'alternativeText' => CBModel::value($spec, 'alternativeText', '', 'trim'),
            'captionAsMarkdown' => CBModel::value($spec, 'captionAsMarkdown', ''),
            'image' => CBModel::value($spec, 'image', null, 'CBImage::specToModel'),
        ];

        $parsedown = new Parsedown();
        $model->captionAsHTML = $parsedown->text($model->captionAsMarkdown);

        return $model;
    }
}

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
        $basename = "{$image->filename}.{$image->extension}";
        $imageURL = CBDataStore::flexpath($image->ID, $basename, CBSiteURL);
        $pageContext = CBPageContext::current();
        $descriptionAsHTML = CBModel::value($pageContext, 'descriptionAsHTML', '');

        CBArtworkElement::render([
            'alternativeText' => CBModel::value($model, 'alternativeText', ''),
            'height' => $image->height,
            'maxWidth' => empty($model->maxWidth) ? $image->width / 2 : $image->maxWidth,
            'width' => $image->width,
            'URL' => $imageURL,
        ]);

        ?>

        <div class="pin">
            <a href="https://www.pinterest.com/pin/create/button/"
               data-pin-custom="true"
               data-pin-description="<?= $descriptionAsHTML ?>"
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
        ];

        if (!empty($spec->image)) {
            $image = $spec->image;
            $model->image = (object)[
                'extension' => $image->extension,
                'filename' => $image->filename,
                'height' => $image->height,
                'ID' => $image->ID,
                'width' => $image->width,
            ];
        }

        return $model;
    }
}

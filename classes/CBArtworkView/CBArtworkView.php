<?php

final class CBArtworkView {

    /**
     * @return null
     */
    static function renderModelAsHTML(stdClass $model) {
        if (empty($model->image)) {
            echo '<!-- CBArtworkView without an image. -->';
            return;
        }

        CBHTMLOutput::requireClassName(__CLASS__);

        echo '<figure class="CBArtworkView">';

        $image = $model->image;
        $basename = "{$image->filename}.{$image->extension}";

        CBArtworkElement::render([
            'height' => $image->height,
            'maxWidth' => empty($model->maxWidth) ? $image->width / 2 : $image->maxWidth,
            'width' => $image->width,
            'URL' => CBDataStore::flexpath($image->ID, $basename, CBSiteURL),
        ]);

        echo '</figure>';
    }

    /**
     * @return [string]
     */
    static function requiredCSSURLs() {
        return [Colby::flexnameForCSSForClass(CBSystemURL, __CLASS__)];
    }

    /**
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

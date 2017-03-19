<?php

final class CBArtworkView {

    /**
     * @param string? $model->alternativeText
     * @param string? $model->captionAsMarkdown
     *
     * @return string
     */
    static function modelToSearchText(stdClass $model) {
        $searchText[] = CBModel::value($model, 'alternativeText', '');
        $searchText[] = CBModel::value($model, 'captionAsMarkdown', '');

        return implode(' ', $searchText);
    }

    /**
     * @param string? $model->alternativeText
     * @param string? $model->captionAsHTML
     * @param stdClass $model->image
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
        $alternativeText = CBModel::value($model, 'alternativeText', '');
        $alternativeTextAsHTML = cbhtml($alternativeText);
        $captionAsHTML = CBModel::value($model, 'captionAsHTML', '');
        $size = CBModel::value($model, 'size', 'rw1600');

        switch ($size) {
            case 'rw320':
                $filename = $size;
                $maxWidth = 160;
                break;
            case 'rw640':
                $filename = $size;
                $maxWidth = 320;
                break;
            case 'rw960':
                $filename = $size;
                $maxWidth = 480;
                break;
            case 'rw1280':
                $filename = $size;
                $maxWidth = 640;
                break;
            case 'rw1920':
                $filename = $size;
                $maxWidth = 960;
                break;
            case 'rw2560':
                $filename = $size;
                $maxWidth = 1280;
                break;
            case 'original':
                $filename = $size;
                $maxWidth = $model->image->width / 2; // retina
                break;
            case 'page':
                $filename = 'original';
                $maxWidth = null;
                break;
            default:
                $filename = 'rw1600';
                $maxWidth = 800;
                break;
        }

        $imageURL = CBDataStore::flexpath($image->ID, "{$filename}.{$image->extension}", CBSitePreferences::siteURL());

        CBArtworkElement::render([
            'alternativeText' => $alternativeText,
            'height' => $image->height,
            'maxWidth' => $maxWidth,
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
     * @param string $spec->captionAsMarkdown
     *
     *      The markdown format is CommonMark.
     *
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
            'size' => CBModel::value($spec, 'size', 'default', 'trim'),
        ];

        $parsedown = new Parsedown();
        $model->captionAsHTML = $parsedown->text($model->captionAsMarkdown);

        return $model;
    }
}

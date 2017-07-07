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
     * @param string? $model->captionAsMarkdown
     *
     *      This will be used as fallback alternative text if alternativeText
     *      is empty.
     *
     * @param object? (CBImage) $model->image
     * @param string? $model->size
     *
     *      The maximum width of the image in retina pixels. rw1600 (800pt) is
     *      the default.
     *
     *      rw320|rw640|rw960|rw1280|rw1600|rw1920|rw2560|original|page
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

        $image = $model->image;
        $alternativeText = CBModel::value($model, 'alternativeText', '');

        /**
         * 2017.06.26
         * After working with customers and being annoyed with copying caption
         * into alternative text, I decided to make this imperfect change and
         * use the caption markdown as fallback alternitive text. It's more
         * imperfect if there's actual markdown formatting, but it still works.
         */

        if (empty($alternativeText)) {
            $alternativeText = mb_substr(CBModel::value($model, 'captionAsMarkdown', '', 'trim'), 0, 100);
        }

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

        if ($maxWidth) {
            $captionDeclarations = "max-width: {$maxWidth}px";
        } else {
            $captionDeclarations = '';
        }

        ?>

        <figure class="CBArtworkView">

            <?php

            $imageURL = CBDataStore::flexpath($image->ID, "{$filename}.{$image->extension}", CBSitePreferences::siteURL());

            CBArtworkElement::render([
                'alternativeText' => $alternativeText,
                'height' => $image->height,
                'maxWidth' => $maxWidth,
                'width' => $image->width,
                'URL' => $imageURL,
            ]);

            if (!empty($captionAsHTML)) {
                ?>

                <figcaption style="<?= $captionDeclarations ?>">
                    <?= $captionAsHTML ?>
                </figcaption>

                <?php
            }

            ?>

            <div class="social" style="<?= $captionDeclarations ?>">
                <a href="https://www.pinterest.com/pin/create/button/"
                   data-pin-custom="true"
                   data-pin-description="<?= $alternativeTextAsHTML ?>"
                   data-pin-do="buttonPin"
                   data-pin-media="<?= $imageURL ?>">
                    Pin to Pinterest
                </a>
            </div>

        </figure>

        <?php
    }

    /**
     * @return [string]
     */
    static function requiredCSSURLs() {
        return [Colby::flexnameForCSSForClass(CBSystemURL, __CLASS__)];
    }

    /**
     * @param string? $spec->alternativeText
     * @param string? $spec->captionAsMarkdown
     *
     *      The markdown format is CommonMark.
     *
     * @param object? (CBImage) $spec->image
     * @param string? $model->size
     *
     *      The maximum width of the image in retina pixels. rw1600 (800pt) is
     *      the default.
     *
     *      rw320|rw640|rw960|rw1280|rw1600|rw1920|rw2560|original|page
     *
     * @return stdClass
     */
    static function specToModel(stdClass $spec) {
        $model = (object)[
            'className' => __CLASS__,
            'alternativeText' => CBModel::value($spec, 'alternativeText', '', 'trim'),
            'captionAsMarkdown' => CBModel::value($spec, 'captionAsMarkdown', ''),
            'image' => CBModel::valueAsSpecToModel($spec, 'image', 'CBImage'),
            'size' => CBModel::value($spec, 'size', 'default', 'trim'),
        ];

        $parsedown = new Parsedown();
        $model->captionAsHTML = $parsedown->text($model->captionAsMarkdown);

        return $model;
    }
}

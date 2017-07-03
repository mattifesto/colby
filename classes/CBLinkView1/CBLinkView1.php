<?php

final class CBLinkView1 {

    /**
     * @param object $model
     *
     * @return null
     */
    static function renderModelAsHTML(stdClass $model) {
        if (empty($model->image)) {
            echo '<!-- CBLinkView1: no image specified -->';
            return;
        }

        $description = CBModel::value($model, 'description', '');
        $image = $model->image;
        $title = CBModel::value($model, 'title', '');
        $size = CBModel::value($model, 'size');
        $URL = CBModel::value($model, 'URL', '');

        switch ($size) {
            case 'small':
                $imageURL = CBDataStore::flexpath($image->ID, "rw480.{$image->extension}", CBSiteURL);
                $imageWidth = 240;
                break;
            case 'large':
                $imageURL = CBDataStore::flexpath($image->ID, "rw960.{$image->extension}", CBSiteURL);
                $imageWidth = 480;
                break;
            default:
                $imageURL = CBDataStore::flexpath($image->ID, "rw640.{$image->extension}", CBSiteURL);
                $imageWidth = 320;
                $size = 'medium';
                break;
        }

        ?>

        <figure class="CBLinkView1 <?= $size ?>">

                <a href="<?= cbhtml($URL) ?>">

                    <?php

                    CBArtworkElement::render([
                        'alternativeText' => $title,
                        'height' => $image->height,
                        'maxWidth' => $imageWidth,
                        'width' => $image->width,
                        'URL' => $imageURL,
                    ]);

                    ?>

                    <div class="text">
                        <figcaption>
                            <div class="title"><?= cbhtml($title) ?></div>
                            <div class="description"><?= cbhtml($description) ?></div>
                        </figcaption>
                        <div class="arrow">
                        </div>
                    </div>
                </a>

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
     * @param object $spec
     *
     * @return object
     */
    static function specToModel(stdClass $spec) {
        return (object)[
            'className' => __CLASS__,
            'description' => CBModel::value($spec, 'description', '', 'trim'),
            'image' => CBModel::value($spec, 'image', null, 'CBImage::specToModel'),
            'size' => CBModel::value($spec, 'size', '', 'trim'),
            'title' => CBModel::value($spec, 'title', '', 'trim'),
            'URL' => CBModel::value($spec, 'URL', '', 'trim'),
        ];
    }
}

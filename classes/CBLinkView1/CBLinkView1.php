<?php

final class CBLinkView1 {

    /**
     * @param model $spec
     *
     * @return ?model
     */
    static function CBModel_build(stdClass $spec): ?stdClass {
        $model = (object)[
            'description' => CBModel::valueToString($spec, 'description'),
            'size' => trim(CBModel::valueToString($spec, 'size')),
            'URL' => trim(CBModel::valueToString($spec, 'URL')),
        ];

        /* image */

        if ($imageSpec = CBModel::valueAsModel($spec, 'image', ['CBImage'])) {
            $model->image = CBModel::build($imageSpec);
        }

        return $model;
    }

    /**
     * @param model $spec
     *
     * @return model
     */
    static function CBModel_upgrade(stdClass $spec): stdClass {
        if ($imageSpec = CBModel::valueAsObject($spec, 'image')) {
            $spec->image = CBImage::fixAndUpgrade($imageSpec);
        }

        return $spec;
    }

    /**
     * @param object $model
     *
     * @return null
     */
    static function CBView_render(stdClass $model) {
        if (empty($model->image)) {
            echo '<!-- CBLinkView1: no image specified -->';
            return;
        }

        $description = CBModel::value($model, 'description', '');
        $image = $model->image;
        $title = CBModel::valueToString($model, 'title');
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
    static function CBHTMLOutput_CSSURLs() {
        return [Colby::flexnameForCSSForClass(CBSystemURL, __CLASS__)];
    }
}

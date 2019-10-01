<?php

final class CBArtworkView {


    /* -- CBHTMLOutput interfaces -- -- -- -- -- */

    /**
     * @return [string]
     */
    static function CBHTMLOutput_CSSURLs() {
        return [
            Colby::flexpath(__CLASS__, 'v374.css', cbsysurl()),
        ];
    }
    /* CBHTMLOutput_CSSURLs() */


    /* -- CBInstall interfaces -- -- -- -- -- */

    /**
     * @return void
     */
    static function CBInstall_install(): void {
        CBViewCatalog::installView(
            __CLASS__
        );
    }
    /* CBInstall_install() */


    /**
     * @return [string]
     */
    static function CBInstall_requiredClassNames(): array {
        return [
            'CBViewCatalog',
        ];
    }
    /* CBInstall_requiredClassNames() */


    /* -- CBModel interfaces -- -- -- -- -- */

    /**
     * @param model $spec
     *
     *      {
     *          alternativeText: ?string
     *          captionAsMarkdown: ?string
     *
     *              The markdown format is CommonMark.
     *
     *          image: ?model
     *          size: ?string
     *
     *              The maximum width of the image in retina pixels. rw1600
     *              (800pt) is the default.
     *
     *              rw320|rw640|rw960|rw1280|rw1600|rw1920|rw2560|original|page
     *      }
     *
     * @return ?object
     */
    static function CBModel_build(stdClass $spec): ?stdClass {
        $model = (object)[
            'alternativeText' => trim(
                CBModel::valueToString($spec, 'alternativeText')
            ),

            'captionAsMarkdown' => CBModel::valueToString(
                $spec,
                'captionAsMarkdown'
            ),

            'CSSClassNames' => CBModel::valueToNames(
                $spec,
                'CSSClassNames'
            ),

            'size' => CBModel::valueAsName(
                $spec,
                'size'
            ),
        ];


        /* image */

        $imageSpec = CBModel::valueAsModel(
            $spec,
            'image',
            [
                'CBImage',
            ]
        );

        if ($imageSpec !== null) {
            $model->image = CBModel::build($imageSpec);
        }


        /* caption */

        $parsedown = new Parsedown();
        $model->captionAsHTML = $parsedown->text($model->captionAsMarkdown);


        /* done */

        return $model;
    }
    /* CBModel_build() */



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
     * @param string? $model->alternativeText
     * @param string? $model->captionAsMarkdown
     *
     * @return string
     */
    static function CBModel_toSearchText(stdClass $model) {
        $searchText[] = CBModel::valueToString($model, 'alternativeText');
        $searchText[] = CBModel::valueToString($model, 'captionAsMarkdown');

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
     * @param [string]? $model->CSSClassNames
     *
     *      "hideSocial"
     *
     * @param object? (CBImage) $model->image
     * @param string? $model->size
     *
     *      The maximum width of the image in retina pixels. rw1600 (800pt) is
     *      the default.
     *
     *      rw320|rw640|rw960|rw1280|rw1600|rw1920|rw2560|original|page
     *
     * @return void
     */
    static function CBView_render(stdClass $model): void {
        if (empty($model->image)) {
            echo '<!-- CBArtworkView without an image. -->';
            return;
        }

        $CSSClassNames = CBModel::valueToArray($model, 'CSSClassNames');

        array_walk($CSSClassNames, 'CBHTMLOutput::requireClassName');

        $CSSClassNames = cbhtml(implode(' ', $CSSClassNames));

        CBHTMLOutput::addPinterest();

        $image = $model->image;

        $alternativeText = CBModel::valueToString(
            $model,
            'alternativeText'
        );


        /**
         * @NOTE 2017_06_26
         *
         * After working with customers and being annoyed with copying caption
         * into alternative text, I decided to make this imperfect change and
         * use the caption markdown as fallback alternitive text. It's more
         * imperfect if there's actual markdown formatting, but it still works.
         */

        if (empty($alternativeText)) {
            $captionAsMarkdown = trim(
                CBModel::valueToString(
                    $model,
                    'captionAsMarkdown'
                )
            );

            $alternativeText = mb_substr(
                $captionAsMarkdown,
                0,
                100
            );
        }

        $alternativeTextAsHTML = cbhtml($alternativeText);

        $captionAsHTML = CBModel::valueToString(
            $model,
            'captionAsHTML'
        );

        $size = CBModel::valueAsName(
            $model,
            'size'
        ) ?? 'rw1600';

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

        <div class="CBArtworkView <?= $CSSClassNames ?>">

            <?php

            $imageURL = CBDataStore::flexpath(
                $image->ID,
                "{$filename}.{$image->extension}",
                cbsiteurl()
            );

            CBArtworkElement::render(
                [
                    'alternativeText' => $alternativeText,
                    'height' => $image->height,
                    'maxWidth' => $maxWidth,
                    'width' => $image->width,
                    'URL' => $imageURL,
                ]
            );

            if (!empty($captionAsHTML)) {
                ?>

                <div class="caption" style="<?= $captionDeclarations ?>">
                    <?= $captionAsHTML ?>
                </div>

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

        </div>

        <?php
    }
    /* CBView_render() */

}

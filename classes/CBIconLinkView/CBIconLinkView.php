<?php

final class CBIconLinkView {

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
     * @param object $spec
     *
     * @return object
     */
    static function CBModel_build(stdClass $spec): stdClass {
        $model = (object)[
            'alternativeText' => CBModel::valueToString(
                $spec,
                'alternativeText'
            ),
            'disableRoundedCorners' => CBModel::valueToBool(
                $spec,
                'disableRoundedCorners'
            ),
            'text' => (
                $text = trim(
                    CBModel::valueToString($spec, 'text')
                )
            ),
            'textAsHTML' => cbhtml($text),
            'textColor' => CBModel::value(
                $spec,
                'textColor',
                null,
                'CBConvert::stringToCSSColor'
            ),
            'URL' => (
                $URL = trim(
                    CBModel::valueToString($spec, 'URL')
                )
            ),
            'URLAsHTML' => cbhtml($URL),
        ];

        /* image */

        if ($imageSpec = CBModel::valueAsModel($spec, 'image', ['CBImage'])) {
            $model->image = CBModel::build($imageSpec);
        }

        return $model;
    }


    /**
     * @param object $model
     *
     * @return string
     */
    static function CBModel_toSearchText(stdClass $model) {
        $strings[] = CBModel::value($model, 'text');
        $strings[] = CBModel::value($model, 'alternativeText');

        return implode(' ', array_filter($strings));
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
     * @param string? $model->URLAsHTML
     * @param string? $model->textColor
     *
     * @return [string:string]
     */
    private static function containerElement(stdClass $model) {
        if (empty($model->URLAsHTML)) {
            $hrefAttribute = '';
            $tagName = 'div';
        } else {
            $hrefAttribute = "href=\"{$model->URLAsHTML}\"";
            $tagName = 'a';
        }

        if (empty($model->textColor)) {
            $styleAttribute = '';
        } else {
            $styleAttribute = "style=\"color: {$model->textColor}\"";
        }

        return [
            'openHTML' => (
                "<{$tagName} " .
                "class=\"container\" " .
                "{$hrefAttribute} " .
                "{$styleAttribute}" .
                ">"
            ),
            'closeHTML' => "</{$tagName}>",
        ];
    }


    /**
     * @param bool? $model->disableRoundedCorners
     * @param object? $model->image
     *
     * @return string
     */
    private static function imageElementHTML(stdClass $model) {
        if ($flexpath = CBImage::valueToFlexpath($model, 'image', 'rw640')) {
            $imageCSS = "background-image: url(/{$flexpath});";
        } else {
            $imageCSS = 'background-color: hsl(30, 50%, 80%)';
        }

        if (empty($model->disableRoundedCorners)) {
            $classes = "icon rounded";
        } else {
            $classes = "icon";
        }

        return "<div class=\"{$classes}\" style=\"{$imageCSS}\"></div>";
    }


    /**
     * @param string? $model->textAsHTML
     *
     * @return string
     */
    private static function textElementHTML(stdClass $model) {
        if (empty($model->textAsHTML)) {
            return '';
        } else {
            return "<div class=\"text\">{$model->textAsHTML}</div>";
        }
    }


    /**
     * @param string? $model->textAsHTML
     * @param string? $model->URLAsHTML
     * @param stdClass? $model->image
     *
     * @return null
     */
    static function CBView_render(stdClass $model) {
        $containerElement = CBIconLinkView::containerElement($model);
        $imageElementHTML = CBIconLinkView::imageElementHTML($model);
        $textElementHTML = CBIconLinkView::textElementHTML($model);

        ?>

        <div class="CBIconLinkView">
            <?= $containerElement['openHTML'] ?>
                <?= $imageElementHTML ?>
                <?= $textElementHTML ?>
            <?= $containerElement['closeHTML'] ?>
        </div>

        <?php
    }


    /**
     * @return [string]
     */
    static function CBHTMLOutput_CSSURLs() {
        return [
            Colby::flexpath(__CLASS__, 'css', cbsysurl()),
        ];
    }
}

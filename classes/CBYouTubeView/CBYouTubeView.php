<?php

final class CBYouTubeView {

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


    /* -- CBHTMLOutput interfaces -- -- -- -- -- */

    /**
     * @return [string]
     */
    static function CBHTMLOutput_CSSURLs(): array {
        return [
            Colby::flexpath(__CLASS__, 'css', cbsysurl()),
        ];
    }


    /**
     * @return [string]
     */
    static function CBHTMLOutput_requiredClassNames(): array {
        return [
            'CBContentStyleSheet',
        ];
    }


    /**
     * @param model $spec
     *
     * @return ?model
     */
    static function CBModel_build(stdClass $spec): ?stdClass {
        return (object)[
            'captionAsMessage' => CBModel::valueToString(
                $spec,
                'captionAsMessage'
            ),
            'videoID' => trim(
                CBModel::valueToString($spec, 'videoID')
            ),
            'width' => trim(
                CBModel::valueToString($spec, 'width')
            ),
        ];
    }


    /**
     * @return void
     */
    static function CBView_render(stdClass $model): void {
        $videoID = CBModel::valueToString($model, 'videoID');

        if (empty($videoID)) {
            echo (
                '<!-- CBYouTubeView: no valid video ID has been set ' .
                'for this view. -->'
            );

            return;
        }

        $captionAsHTML = CBMessageMarkup::messageToHTML(
            CBModel::valueToString($model, 'captionAsMessage')
        );

        $width = CBModel::valueToString($model, 'width');

        switch ($width) {
            case "320":
            case "480":
            case "640":
            case "960":
            case "1280":
                $width = "{$width}px";
                break;
            case "page":
                $width = "100%";
                break;
            default:
                $width = "800px";
                break;
        }

        ?>

        <div class="CBYouTubeView">
            <div class="width" style="width: <?= $width ?>">
                <div class="aspect">
                    <iframe
                        src="<?= "https://www.youtube.com/embed/{$videoID}" ?>"
                        allowfullscreen
                    ></iframe>
                </div>
                <?php if (!empty($captionAsHTML)) { ?>
                    <div class="caption CBContentStyleSheet">
                        <?= $captionAsHTML ?>
                    </div>
                <?php } ?>
            </div>
        </div>

        <?php
    }
}

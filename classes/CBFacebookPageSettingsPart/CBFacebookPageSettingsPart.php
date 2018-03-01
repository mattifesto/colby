<?php

final class CBFacebookPageSettingsPart {

    /**
     * @return void
     */
    static function CBPageSettings_renderHeadElementHTML(): void {
        $info = CBHTMLOutput::pageInformation();

        ?>

        <meta property="fb:app_id" content="<?= CBFacebookAppID ?>">
        <meta property="og:title" content="<?= cbhtml(CBModel::valueToString($info, 'title')) ?>">
        <meta property="og:description" content="<?= cbhtml(CBModel::valueToString($info, 'description')) ?>">

        <?php

        if ($image = CBModel::valueAsModel($info, 'image', ['CBImage'])) {
            $imageURL = CBImage::asFlexpath($image, 'rw1280', cbsysurl());
        } else {
            $imageURL = CBModel::valueToString($info, 'imageURL');
        }

        if (!empty($imageURL)) {
            ?>

            <meta property="og:image" content="<?= $imageURL ?>">

            <?php
        }
    }

}

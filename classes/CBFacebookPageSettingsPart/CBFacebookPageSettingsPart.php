<?php

final class CBFacebookPageSettingsPart {

    /* -- CBPageSettings interfaces -- -- -- -- -- */



    /**
     * @return void
     */
    static function CBPageSettings_renderHeadElementHTML(): void {
        $info = CBHTMLOutput::pageInformation();

        $facebookAppID = CBFacebookPreferences::getAppID();

        if ($facebookAppID !== '') {
            ?>

            <meta property="fb:app_id" content="<?= $facebookAppID ?>">

            <?php
        }

        ?>

        <meta
            property="og:title"
            content="<?=

                cbhtml(
                    CBModel::valueToString(
                        $info,
                        'title'
                    )
                )

            ?>"
        >

        <meta
            property="og:description"
            content="<?=

                cbhtml(
                    CBModel::valueToString(
                        $info,
                        'description'
                    )
                )

            ?>"
        >

        <?php

        $image = CBModel::valueAsModel(
            $info,
            'image',
            [
                'CBImage',
            ]
        );

        if ($image !== null) {
            $imageURL = CBImage::asFlexpath(
                $image,
                'rw1280',
                cbsysurl()
            );
        } else {
            $imageURL = CBModel::valueToString($info, 'imageURL');
        }

        if (!empty($imageURL)) {
            ?>

            <meta property="og:image" content="<?= $imageURL ?>">

            <?php
        }
    }
    /* CBPageSettings_renderHeadElementHTML() */

}

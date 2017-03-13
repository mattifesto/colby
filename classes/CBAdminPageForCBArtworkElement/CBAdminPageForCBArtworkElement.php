<?php

final class CBAdminPageForCBArtworkElement {

    /**
     * @return [string]
     */
    static function adminPageMenuNamePath() {
        return ['help', 'CBArtworkElement'];
    }

    /**
     * @return stdClass
     */
    static function adminPagePermissions() {
        return (object)['group' => 'Developers'];
    }

    /**
     * @return null
     */
    static function adminPageRenderContent() {
        CBHTMLOutput::setTitleHTML('CBArtworkElement');
        CBHTMLOutput::setDescriptionHTML('Description and examples of CBArtworkElement functionality.');

        $URL = CBDataStore::flexpath(CBAdminPageForTests::imageID, 'rw320.jpeg', CBSiteURL);

        ?>

        <style>

            main {
                background-color: hsl(0, 0%, 80%);
            }

            .CBArtworkElement {
                background-color: red;
                margin-bottom: 50px;
            }

            .content {
                background-color: white;
                line-height: 1.6;
                margin: 0 auto;
                max-width: 640px;
                padding: 50px 10px 100px;
            }

            .content > * + * {
                margin-top: 10px;
            }

        </style>
        <div class="content">
            <div>
                Below is the text image used on this page. It's the rw320 size of
                the system test image. The original image is 1600x900, but this
                image is 320x180.
            </div>
            <div style="margin-bottom: 50px; text-align: center">
                <img src="<?= $URL ?>">
            </div>
            <div>
                Below is an artwork element with an aspect ratio of 16 x 9 and
                no maxWidth specified. The image is always stretch to fill the
                available width.
            </div>
            <?php

            CBArtworkElement::render([
                'height' => 9,
                'width' => 16,
                'URL' => $URL,
            ]);

            ?>
            <div>
                Below is an artwork element with a wider aspect ratio of 32 x 9
                and no maxWidth specified. The image is clipped vertically
                because the aspect ratio always takes priority.
            </div>
            <?php

            CBArtworkElement::render([
                'height' => 9,
                'width' => 32,
                'URL' => $URL,
            ]);

            ?>
            <div>
                Below is an artwork element with a more narrow aspect ratio of
                15 x 9 and no maxWidth specified. The image has extra space at
                the bottom because it reserved space for the apect ratio and the
                image was not able to fill it. Empty space for this document
                displays as red.
            </div>
            <?php

            CBArtworkElement::render([
                'height' => 9,
                'width' => 15,
                'URL' => $URL,
            ]);

            ?>
            <div>
                Below is an artwork element with a 16 x 9 aspect ratio and
                maxWidth of 480px. The artwork element is 480px wide until the
                available width shrinks and then the artwork element shrinks as
                well.
            </div>
            <?php

            CBArtworkElement::render([
                'height' => 9,
                'maxWidth' => 480,
                'width' => 16,
                'URL' => $URL,
            ]);

            ?>

        </div>

        <?php
    }
}

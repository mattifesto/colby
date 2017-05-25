<?php

final class CBDefaultPageHeaderView {

    /**
     * @return null
     */
    static function renderModelAsHTML(stdClass $model) {
        ?>

        <header class="CBDefaultPageHeaderView">
            <?= cbhtml(CBSitePreferences::siteName()); ?>
        </header>

        <?php
    }

    /**
     * @return [string]
     */
    static function requiredCSSURLs() {
        return [Colby::flexnameForCSSForClass(CBSystemURL, __CLASS__)];
    }
}

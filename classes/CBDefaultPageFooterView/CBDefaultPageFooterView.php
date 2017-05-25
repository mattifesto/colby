<?php

final class CBDefaultPageFooterView {

    /**
     * @return null
     */
    static function renderModelAsHTML(stdClass $model) {
        ?>

        <footer class="CBDefaultPageFooterView">
            <div class="copyright">
                Copyright &copy; <?= gmdate('Y'), ' ', cbhtml(CBSitePreferences::siteName()); ?>
            </div>
        </footer>

        <?php
    }

    /**
     * @return [string]
     */
    static function requiredCSSURLs() {
        return [Colby::flexnameForCSSForClass(CBSystemURL, __CLASS__)];
    }
}

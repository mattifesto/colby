<?php

final class CBDefaultPageFooterView {

    /**
     * @return null
     */
    static function CBView_render(stdClass $model) {
        ?>

        <footer class="CBDefaultPageFooterView CBDarkTheme">
            <div class="copyright">
                Copyright &copy; <?= gmdate('Y') . ' ' . cbhtml(CBSitePreferences::siteName()) ?>
            </div>
        </footer>

        <?php
    }

    /**
     * @return [string]
     */
    static function CBHTMLOutput_CSSURLs() {
        return [Colby::flexnameForCSSForClass(CBSystemURL, __CLASS__)];
    }
}

<?php

final class CBDefaultPageFooterView {

    /**
     * @return [string]
     */
    static function CBHTMLOutput_CSSURLs(): array {
        return [
            Colby::flexpath(__CLASS__, 'css', cbsysurl()),
        ];
    }


    /**
     * @return null
     */
    static function CBView_render(stdClass $model) {
        ?>

        <footer class="CBDefaultPageFooterView CBDarkTheme">
            <div class="copyright">
                Copyright &copy; <?=
                    gmdate('Y') .
                    ' ' .
                    cbhtml(CBSitePreferences::siteName())
                ?>
            </div>
        </footer>

        <?php
    }
}

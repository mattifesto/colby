<?php

final class PREFIXPageFooterView {

    /**
     * @return [string]
     */
    static function CBHTMLOutput_CSSURLs(): array {
        return [Colby::flexpath(__CLASS__, 'css', cbsiteurl())];
    }

    /**
     * @param model $model
     *
     * @return void
     */
    static function CBView_render(stdClass $model): void {
        ?>

        <footer class="PREFIXPageFooterView CBDarkTheme">
            <div class="copyright">
                <span>
                    Copyright &copy; <?= gmdate('Y') . ' ' . cbhtml(CBSitePreferences::siteName()) ?>
                </span>
            </div>
        </footer>

        <?php
    }
}

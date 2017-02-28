<?php

/**
 * This view is meant to be used directly by all admin page handlers to render
 * the standard administration page footer.
 */
final class CBAdminPageFooterView {

    /**
     * @return null
     */
    static function renderModelAsHTML(stdClass $model = null) {
        CBHTMLOutput::requireClassName(__CLASS__);

        ?>

        <section class="CBAdminPageFooterView">
            <ul>
                <li>Copyright &copy; 2012-<?php echo gmdate('Y'); ?> Mattifesto Design</li>

                <?php

                if (ColbyUser::current()->isLoggedIn()) {
                    $logoutURLForHTML   = ColbyConvert::textToHTML(ColbyUser::logoutURL());
                    $userName           = ColbyUser::userRow()->facebookName;
                    $userNameHTML       = ColbyConvert::textToHTML($userName);

                    ?>

                    <li><?php echo $userNameHTML; ?></li>
                    <li><a href="<?php echo $logoutURLForHTML; ?>">log out</a></li>

                    <?php
                }

                ?>

            </ul>
        </section>

        <?php
    }

    /**
     * @return [string]
     */
    static function requiredCSSURLs() {
        return [Colby::flexnameForCSSForClass(CBSystemURL, __CLASS__)];
    }
}

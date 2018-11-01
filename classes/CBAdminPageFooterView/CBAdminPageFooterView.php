<?php

/**
 * This view is meant to be used directly by all admin page handlers to render
 * the standard administration page footer.
 */
final class CBAdminPageFooterView {

    /**
     * @return null
     */
    static function CBView_render(stdClass $model) {
        ?>

        <div class="CBAdminPageFooterView">
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
        </div>

        <?php
    }

    /**
     * @return [string]
     */
    static function CBHTMLOutput_CSSURLs() {
        return [Colby::flexpath(__CLASS__, 'css', cbsysurl())];
    }
}

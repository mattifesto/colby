<?php

/**
 * This view is meant to be used directly by all admin page handlers to render
 * the standard administration page footer.
 */
final class CBAdminPageFooterView {

    /* -- CBView interfaces -- -- -- -- -- */



    /**
     * @param object $model
     *
     * @return void
     */
    static function CBView_render(stdClass $model): void {
        ?>

        <div class="CBAdminPageFooterView">
            <ul>
                <li>
                    Copyright &copy; 2012-<?= gmdate('Y'); ?>
                    Mattifesto Design
                </li>

                <?php

                if (ColbyUser::currentUserIsLoggedIn()) {
                    $logoutURLAsHTML = cbhtml(ColbyUser::logoutURL());
                    $userName = ColbyUser::userRow()->facebookName;
                    $userNameAsHTML = cbhtml($userName);

                    ?>

                    <li>
                        <?= $userNameAsHTML ?>
                    </li>
                    <li>
                        <a href="<?= $logoutURLAsHTML ?>">log out</a>
                    </li>

                    <?php
                }

                ?>

            </ul>
        </div>

        <?php
    }
    /* CBView_render() */



    /* -- CBHTMLOutput interfaces -- -- -- -- -- */



    /**
     * @return [string]
     */
    static function CBHTMLOutput_CSSURLs(): array {
        return [
            Colby::flexpath(__CLASS__, 'css', cbsysurl()),
        ];
    }
    /* CBHTMLOutput_CSSURLs() */

}

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
        $currentUserID = ColbyUser::getCurrentUserCBID();

        ?>

        <div class="CBAdminPageFooterView">
            <ul>
                <li>
                    Copyright &copy; 2012-<?= gmdate('Y'); ?>
                    Mattifesto Design
                </li>

                <?php

                if ($currentUserID !== null) {
                    $currentUserModel = CBModelCache::fetchModelByID(
                        $currentUserID
                    );

                    $logoutURL = ColbyUser::logoutURL();

                    $userName = CBModel::valueToString(
                        $currentUserModel,
                        'title'
                    );

                    ?>

                    <li>
                        <?= cbhtml($userName) ?>
                    </li>
                    <li>
                        <a href="<?= cbhtml($logoutURL) ?>">log out</a>
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

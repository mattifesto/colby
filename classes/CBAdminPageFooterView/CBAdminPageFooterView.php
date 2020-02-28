<?php

/**
 * This view is meant to be used directly by all admin page handlers to render
 * the standard administration page footer.
 */
final class CBAdminPageFooterView {

    /* -- CBHTMLOutput interfaces -- -- -- -- -- */



    /**
     * @return [string]
     */
    static function CBHTMLOutput_CSSURLs(): array {
        return [
            Colby::flexpath(__CLASS__, 'css', cbsysurl()),
        ];
    }



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

                    $userName = CBModel::valueToString(
                        $currentUserModel,
                        'title'
                    );

                    $currentUserAdminPageURL = (
                        '/admin/' .
                        '?c=CBAdminPageForUserSettings' .
                        "&hash={$currentUserID}"
                    );

                    ?>

                    <li>
                        <a href="<?= cbhtml($currentUserAdminPageURL) ?>">
                            <?= cbhtml($userName) ?>
                        </a>
                    </li>

                    <?php
                }

                ?>

            </ul>
        </div>

        <?php
    }
    /* CBView_render() */

}

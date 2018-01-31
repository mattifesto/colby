<?php

final class CBUsersAdmin {

    /**
     * @return [string]
     */
    static function adminPageMenuNamePath() {
        return ['general', 'users'];
    }

    /**
     * @return stdClass
     */
    static function adminPagePermissions() {
        return (object)['group' => 'Administrators'];
    }

    /**
     * @return void
     */
    static function adminPageRenderContent() {
        CBHTMLOutput::setTitleHTML('Users');
        CBHTMLOutput::setDescriptionHTML('Tools for viewing and editing site users.');

        $users = CBUsersAdmin::fetchUsers();

        ?><div class="list"><?php

            foreach ($users as $user) {
                $userPageURI = "/admin/page/?class=CBAdminPageForUserSettings&amp;hash={$user->hash}";

                ?>

                <div class="user">
                    <a href="<?= $userPageURI ?>"><?= $user->facebookName ?></a>
                </div>

                <?php
            }

        ?></div><?php
    }

    /**
     * @return [stdClass]
     */
    private static function fetchUsers() {
        $SQL = <<<EOT

            SELECT      LOWER(HEX(`hash`)) as `hash`, `facebookName`
            FROM        `ColbyUsers`
            ORDER BY    `facebookName`

EOT;

        return CBDB::SQLToObjects($SQL);
    }

    /**
     * @return [string]
     */
    static function CBHTMLOutput_CSSURLs() {
        return [Colby::flexpath(__CLASS__, 'v377.css', cbsysurl())];
    }
}

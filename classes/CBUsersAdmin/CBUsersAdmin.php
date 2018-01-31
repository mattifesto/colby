<?php

final class CBUsersAdmin {

    /**
     * @return [string]
     */
    static function CBAdmin_menuNamePath(): array {
        return ['general', 'users'];
    }

    /**
     * @return void
     */
    static function CBAdmin_render(): void {
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
     * @return [string]
     */
    static function CBHTMLOutput_CSSURLs() {
        return [Colby::flexpath(__CLASS__, 'v376.css', cbsysurl())];
    }

    /**
     * @return void
     */
    static function CBInstall_install(): void {
        $spec = CBModels::fetchSpecByID(CBGeneralAdminMenu::ID);

        $spec->items[] = (object)[
            'className' => 'CBMenuItem',
            'name' => 'users',
            'text' => 'Users',
            'URL' => '/admin/?c=CBUsersAdmin',
        ];

        CBDB::transaction(function () use ($spec) {
            CBModels::save($spec);
        });
    }

    /**
     * @return [string]
     */
    static function CBInstall_requiredClassNames(): array {
        return ['CBGeneralAdminMenu'];
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
}

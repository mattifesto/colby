<?php

final class CBGitHistoryAdmin {

    /* -- CBAdmin interfaces -- -- -- -- -- */



    /**
     * @return string
     */
    static function CBAdmin_getUserGroupClassName(): string {
        return 'CBAdministratorsUserGroup';
    }



    /**
     * @return [string]
     */
    static function CBAdmin_menuNamePath(): array {
        return [
            'general',
            'git_history'
        ];
    }



    /**
     * @return void
     */
    static function CBAdmin_render(): void {
        CBHTMLOutput::pageInformation()->title = 'Git History';
    }



    /* -- CBAjax interfaces -- -- -- -- -- */



    /**
     * @param mixed $args
     *
     * @return mixed
     */
    static function CBAjax_fetch($args) {
        $year = CBModel::valueAsInt($args, 'year');
        $month = CBModel::valueAsInt($args, 'month');
        $year2 = ($month < 12) ? $year : $year + 1;
        $month2 = ($month < 12) ? $month + 1 : 1;
        $submodule = CBModel::valueToString($args, 'submodule');
        $submodule = preg_replace('/[^a-zA-Z0-9_-]/', '', $submodule);

        $command = 'git log ' .
            "--after=\"{$year}-{$month}-01 00:00\" " .
            "--before=\"{$year2}-{$month2}-01 00:00\" " .
            '--simplify-by-decoration ' .
            '--reverse ' .
            '--pretty=format:"----------------------------------------%n%s%n%ad%n%n%b%n" ' .
            '--date=format:"%Y/%m/%d %l:%M %p" ' .
            '| cat';

        chdir(cbsitedir() . "/{$submodule}");
        exec($command, $output);

        $output = CBMessageMarkup::stringToMarkup(
            implode(
                "\n",
                $output
            )
        );

        $output = <<<EOT

            --- pre\n{$output}
            ---

        EOT;

        return $output;
    }
    /* CBAjax_fetch() */



    /**
     * @return string
     */
    static function CBAjax_fetch_getUserGroupClassName(): string {
        return 'CBAdministratorsUserGroup';
    }



    /* -- CBHTMLOutput interfaces -- -- -- -- -- */



    /**
     * @return [string]
     */
    static function CBHTMLOutput_JavaScriptURLs(): array {
        return [
            Colby::flexpath(__CLASS__, 'v529.js', cbsysurl()),
        ];
    }



    /**
     * @return [[<name>, <value>]]
     */
    static function CBHTMLOutput_JavaScriptVariables(): array {
        return [
            [
                'CBGitHistoryAdmin_submodules',
                CBGit::submodules(),
            ]
        ];
    }



    /**
     * @return [string]
     */
    static function CBHTMLOutput_requiredClassNames(): array {
        return [
            'CBErrorHandler',
            'CBUI',
            'CBUIExpander',
            'CBUINavigationView',
            'CBUISelector',
            'Colby',
        ];
    }



    /* -- CBInstall interfaces -- -- -- -- -- */



    /**
     * @return void
     */
    static function CBInstall_install(): void {
        $updater = CBModelUpdater::fetch(
            (object)[
                'ID' => CBGeneralAdminMenu::getModelCBID(),
            ]
        );

        $items = CBModel::valueToArray(
            $updater->working,
            'items'
        );

        array_push(
            $items,
            (object)[
                'className' => 'CBMenuItem',
                'name' => 'git_history',
                'text' => 'History',
                'URL' => CBAdmin::getAdminPageURL(
                    'CBGitHistoryAdmin'
                ),
            ]
        );

        $updater->working->items = $items;

        CBModelUpdater::save($updater);
    }
    /* CBInstall_install() */



    /**
     * @return [string]
     */
    static function CBInstall_requiredClassNames(): array {
        return [
            'CBGeneralAdminMenu'
        ];
    }

}

<?php

final class CBGitAdmin {

    /**
     * @return [string]
     */
    static function CBAdmin_menuNamePath(): array {
        return ['develop', 'git', 'history'];
    }

    /**
     * @return void
     */
    static function CBAdmin_render(): void {
        CBHTMLOutput::pageInformation()->title = 'Git History';
    }

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

        $output = CBMessageMarkup::stringToMarkup(implode("\n", $output));
        $output = <<<EOT

            --- pre\n{$output}
            ---

EOT;

        return $output;
    }

    static function CBAjax_fetch_group(): string {
        return 'Developers';
    }

    /**
     * @return [string]
     */
    static function CBHTMLOutput_JavaScriptURLs(): array {
        return [Colby::flexpath(__CLASS__, 'v397.js', cbsysurl())];
    }

    /**
     * @return [[<name>, <value>]]
     */
    static function CBHTMLOutput_JavaScriptVariables(): array {
        chdir(cbsitedir());
        exec('git submodule--helper list', $submodules);

        $submodules = array_map(function ($item) {
            $columns = preg_split('/\s/', $item);
            return $columns[3];
        }, $submodules);

        return [
            ['CBGitAdmin_submodules', $submodules]
        ];
    }
    /**
     * @return [string]
     */
    static function CBHTMLOutput_requiredClassNames(): array {
        return ['CBUI', 'CBUIExpander', 'CBUISelector'];
    }

    /**
     * @return void
     */
    static function CBInstall_install(): void {
        $spec = CBModels::fetchSpecByID(CBGitAdminMenu::ID());

        $spec->items[] = (object)[
            'className' => 'CBMenuItem',
            'name' => 'history',
            'text' => 'History',
            'URL' => '/admin/?c=CBGitAdmin',
        ];

        CBDB::transaction(function () use ($spec) {
            CBModels::save($spec);
        });
    }

    /**
     * @return [string]
     */
    static function CBInstall_requiredClassNames(): array {
        return ['CBGitAdminMenu'];
    }
}

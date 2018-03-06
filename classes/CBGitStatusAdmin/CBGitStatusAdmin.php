<?php

final class CBGitStatusAdmin {

    /**
     * @return string
     */
    static function CBAdmin_group(): string {
        return 'Developers';
    }

    /**
     * @return [string]
     */
    static function CBAdmin_menuNamePath(): array {
        return ['develop', 'git', 'status'];
    }

    /**
     * @return void
     */
    static function CBAdmin_render(): void {
        CBHTMLOutput::pageInformation()->title = 'Git Status';
    }

    /**
     * @param mixed $args
     *
     * @return mixed
     */
    static function CBAjax_fetchStatus($args) {
        $results = [];
        $submodules = CBGit::submodules();

        array_unshift($submodules, '');

        foreach ($submodules as $submodule) {
            $results[] = CBGitStatusAdmin::fetchStatus($submodule);
        }

        return $results;
    }

    /**
     * @return string
     */
    static function CBAjax_fetchStatus_group(): string {
        return 'Developers';
    }

    /**
     * @return [string]
     */
    static function CBHTMLOutput_CSSURLs(): array {
        return [Colby::flexpath(__CLASS__, 'v397.css', cbsysurl())];
    }

    /**
     * @return [string]
     */
    static function CBHTMLOutput_JavaScriptURLs(): array {
        return [Colby::flexpath(__CLASS__, 'v397.js', cbsysurl())];
    }

    /**
     * @return [string]
     */
    static function CBHTMLOutput_requiredClassNames(): array {
        return ['CBUI', 'CBUIExpander'];
    }

    /**
     * @return void
     */
    static function CBInstall_install(): void {
        $spec = CBModels::fetchSpecByID(CBGitAdminMenu::ID());

        $spec->items[] = (object)[
            'className' => 'CBMenuItem',
            'name' => 'status',
            'text' => 'Status',
            'URL' => '/admin/?c=CBGitStatusAdmin',
        ];

        CBDB::transaction(function () use ($spec) {
            CBModels::save($spec);
        });
    }

    /**
     * @return [string]
     */
    static function CBInstall_requiredClassNames(): array {
        return ['CBGitAdminMenu', 'CBGitHistoryAdmin'];
    }

    /**
     * @return stdClass
     */
    static function fetchStatus(string $directory): stdClass {
        $message = '';

        chdir(cbsitedir() . "/{$directory}");
        exec('git status --porcelain', $lines);

        if (!empty($lines)) {
            $status = implode("\n", $lines);
            $message = <<<EOT

                --- pre\n{$status}
                ---

EOT;
        }

        if (!empty($message)) {
            $message = (empty($directory) ? 'website' : $directory) .
                "\n\n{$message}";
        }

        return (object)[
            'location' => empty($directory) ? 'website' : $directory,
            'message' => $message,
        ];
    }
}

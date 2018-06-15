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
        return [Colby::flexpath(__CLASS__, 'v430.js', cbsysurl())];
    }

    /**
     * @return [string]
     */
    static function CBHTMLOutput_requiredClassNames(): array {
        return [
            'CBUI',
            'CBUIExpander',
            'CBUISectionItem4',
            'CBUIStringsPart',
        ];
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
     * @param string $command
     *
     * @return string
     *
     *      Returns a multiline message with the command output and potentially
     *      error information.
     */
    private static function exec(string $command): string {
        $lines = [];

        exec("{$command} 2>&1", $lines, $returnValue);

        if ($returnValue !== 0) {
            array_unshift($lines, '');
            array_unshift($lines, "! returned error code: {$returnValue}");
            array_unshift($lines, "$ {$command}");
        }

        $lines = array_map(function ($line) {
            return CBMessageMarkup::stringToMessage($line);
        }, $lines);

        $lines = implode("\n", $lines);

        return <<<EOT

            --- pre CBGitStatusAdmin_pre\n{$lines}
            ---

EOT;
    }

    /**
     * @return stdClass
     */
    static function fetchStatus(string $directory): stdClass {
        $message = '';

        chdir(cbsitedir() . "/{$directory}");

        $message .= CBGitStatusAdmin::exec('git describe');

        $lines = [];
        exec('git status --porcelain', $lines);

        if (!empty($lines)) {
            $lines = implode("\n", $lines);
            $message .= <<<EOT

                --- pre CBGitStatusAdmin_pre\n{$lines}
                ---

EOT;
        }

        $lines = [];
        exec('git remote', $lines);

        if (array_search('origin', $lines) === false) {
            $range = '';
        } else {
            $range = 'origin/master..head';
        }

        $lines = [];
        exec("git log {$range} --oneline --no-decorate --reverse", $lines);

        if (!empty($lines)) {
            $lines = implode("\n", $lines);
            $message .= <<< EOT

                --- pre CBGitStatusAdmin_pre\n{$lines}
                ---
EOT;
        }

        $message = (empty($directory) ? 'website' : $directory) .
            "\n\n{$message}";

        return (object)[
            'location' => empty($directory) ? 'website' : $directory,
            'message' => $message,
        ];
    }
}

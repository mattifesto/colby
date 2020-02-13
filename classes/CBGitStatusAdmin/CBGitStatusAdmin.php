<?php

final class CBGitStatusAdmin {

    /* -- CBAdmin interfaces -- -- -- -- -- */



    /**
     * @return string
     */
    static function CBAdmin_getUserGroupClassName(): string {
        return 'CBDevelopersUserGroup';
    }



    /**
     * @return [string]
     */
    static function CBAdmin_menuNamePath(): array {
        return [
            'develop',
            'git',
            'status',
        ];
    }



    /**
     * @return void
     */
    static function CBAdmin_render(): void {
        CBHTMLOutput::pageInformation()->title = 'Git Status';
    }



    /* -- CBAjax interfaces -- -- -- -- -- */



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
    static function CBAjax_fetchStatus_getUserGroupClassName(): string {
        return 'CBDevelopersUserGroup';
    }



    /* -- CBHTMLOutput interfaces -- -- -- -- -- */



    /**
     * @return [string]
     */
    static function CBHTMLOutput_CSSURLs(): array {
        return [
            Colby::flexpath(__CLASS__, 'v463.css', cbsysurl()),
        ];
    }



    /**
     * @return [string]
     */
    static function CBHTMLOutput_JavaScriptURLs(): array {
        return [
            Colby::flexpath(__CLASS__, 'v529.js', cbsysurl()),
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
            'CBUISectionItem4',
            'CBUIStringsPart',
            'Colby',
        ];
    }



    /* -- CBInstall interfaces -- -- -- -- -- */



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

        CBDB::transaction(
            function () use ($spec) {
                CBModels::save($spec);
            }
        );
    }



    /**
     * @return [string]
     */
    static function CBInstall_requiredClassNames(): array {
        return [
            'CBGitAdminMenu',
            'CBGitHistoryAdmin',
        ];
    }



    /* -- functions -- -- -- -- -- */



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

        if (empty($lines)) {
            return '';
        } else {
            return <<<EOT

                --- pre CBGitStatusAdmin_pre\n{$lines}
                ---

            EOT;
        }
    }



    /**
     * This function lists the commits that have been made to the local branch
     * that have not yet been pushed to the remote branch.
     *
     * @return object
     */
    static function fetchStatus(string $directory): stdClass {
        $location = empty($directory) ? 'website' : $directory;
        $locationAsMessage = CBMessageMarkup::stringToMessage($location);

        $message = <<<EOT

            {$locationAsMessage}

        EOT;

        chdir(cbsitedir() . "/{$directory}");

        $message .= CBGitStatusAdmin::exec('git describe');
        $message .= CBGitStatusAdmin::exec('git status --porcelain');

        $lines = [];

        /**
         * Get the name of the remote tracking branch. This will be something
         * like "origin/master" or "origin/5.x".
         */

        exec(
            CBConvert::stringToCleanLine(<<<EOT

                git for-each-ref
                --format='%(upstream:short)'
                "$(git symbolic-ref -q HEAD)"

            EOT),
            $lines
        );

        if (empty($lines)) {
            $message .= <<<EOT

                This repository is not currently tracking a remote branch.

            EOT;
        } else {
            $remoteBranch = trim($lines[0]);
            $range = "{$remoteBranch}..HEAD";

            $message .= CBGitStatusAdmin::exec(
                "git log {$range} --oneline --no-decorate --reverse"
            );
        }

        return (object)[
            'location' => $location,
            'message' => $message,
        ];
    }
    /* fetchStatus() */

}

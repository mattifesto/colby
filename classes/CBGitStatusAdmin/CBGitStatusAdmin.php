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
            Colby::flexpath(__CLASS__, 'v640.css', cbsysurl()),
        ];
    }



    /**
     * @return [string]
     */
    static function CBHTMLOutput_JavaScriptURLs(): array {
        return [
            Colby::flexpath(__CLASS__, 'v612.js', cbsysurl()),
        ];
    }



    /**
     * @return [string]
     */
    static function CBHTMLOutput_requiredClassNames(): array {
        return [
            'CBUI',
            'CBUIExpander',
            'CBUIPanel',
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
     * @return cbmessage
     *
     *      Returns a multiline cbmessage with the command output and
     *      potentially error information.
     */
    private static function exec(
        string $command
    ): string {
        $lines = [];

        exec(
            "{$command} 2>&1",
            $lines,
            $returnValue
        );

        if ($returnValue !== 0) {
            array_unshift(
                $lines,
                ''
            );

            array_unshift(
                $lines,
                "! returned error code: {$returnValue}"
            );

            array_unshift(
                $lines,
                "$ {$command}"
            );
        }

        $linesAsCBMessage = array_map(
            function ($line) {
                return CBMessageMarkup::stringToMessage($line);
            },
            $lines
        );

        $linesAsCBMessage = implode(
            "\n",
            $linesAsCBMessage
        );

        if (empty($linesAsCBMessage)) {
            return '';
        } else {
            $commandAsCBMessage = CBMessageMarkup::stringToMessage(
                $command
            );

            return <<<EOT

                --- p CBGitStatusAdmin_command
                {$commandAsCBMessage}
                ---

                --- pre CBGitStatusAdmin_pre\n{$linesAsCBMessage}
                ---

            EOT;
        }
    }
    /* exec() */



    /**
     * This function lists the commits that have been made to the local branch
     * that have not yet been pushed to the remote branch.
     *
     * @return object
     */
    static function fetchStatus(
        string $directory
    ): stdClass {
        $location = empty($directory) ? 'website' : $directory;
        $locationAsMessage = CBMessageMarkup::stringToMessage($location);

        $cbmessage = <<<EOT

            {$locationAsMessage}

        EOT;

        chdir(cbsitedir() . "/{$directory}");

        $cbmessage .= CBGitStatusAdmin::exec('git branch');
        $cbmessage .= CBGitStatusAdmin::exec('git describe');
        $cbmessage .= CBGitStatusAdmin::exec('git status --porcelain');

        $currentBranch = CBGit::getCurrentBranchName();
        $currentTrackecBranch = CBGit::getCurrentTrackedBranchName();

        if (
            empty($currentBranch) ||
            empty($currentTrackecBranch)
        ) {
            $cbmessage .= <<<EOT

                --- dl
                    --- dt
                    current branch
                    ---
                    --- dd
                    {$currentBranch}
                    ---
                    --- dt
                    current tracked branch
                    ---
                    --- dd
                    {$currentTrackecBranch}
                    ---
                ---

                The current branch or tracked branch are not valid.

            EOT;
        } else {
            $range = "{$currentTrackecBranch}..{$currentBranch}";

            $cbmessage .= CBGitStatusAdmin::exec(
                "git log {$range} --oneline --no-decorate --reverse"
            );
        }

        return (object)[
            'location' => $location,
            'message' => $cbmessage,
        ];
    }
    /* fetchStatus() */

}

<?php

final class CBGit {

    /* -- functions -- */



    /**
     * @return string
     */
    static function getCurrentBranchName(
    ): string {
        exec(
            'git branch --show-current',
            $outputLines
        );

        if (count($outputLines) > 0) {
            return $outputLines[0];
        } else {
            return '';
        }
    }
    /* getCurrentBranchName() */



    /**
     * @return string
     */
    static function getCurrentTrackedBranchName(
    ): string {
        $command = implode(
            ' ',
            [
                'git for-each-ref',
                '--format=\'%(upstream:short)\'',
                '"$(git symbolic-ref -q HEAD)"',
            ]
        );

        exec(
            $command,
            $outputLines
        );

        if (count($outputLines) > 0) {
            return $outputLines[0];
        } else {
            return '';
        }
    }
    /* getCurrentTrackedBranchName() */



    /**
     * @return [string]
     */
    static function getSubmoduleURLs(
    ): array {
        $pwd = getcwd();

        chdir(
            cbsitedir()
        );

        try {
            $command = (
                'git config --file .gitmodules --get-regexp url ' .
                '| awk \'{ print $2 }\''
            );

            exec(
                $command,
                $submoduleURLs
            );
        } finally {
            chdir($pwd);
        }

        return $submoduleURLs;
    }
    /* getSubmoduleURLs() */



    /**
     * @param string $command
     * @param [string] &$output
     * @param mixed &$exitCode
     *
     * @return void
     */
    static function exec(
        string $command,
        array &$output,
        &$exitCode
    ): void {
        $pwd = getcwd();

        chdir(
            cbsitedir()
        );

        try {
            CBExec::exec(
                $command,
                $output,
                $exitCode
            );
        } finally {
            chdir($pwd);
        }
    }
    /* exec() */



    /**
     * @param array &$output
     * @param mixed &$exitCode
     *
     * @return void
     */
    static function
    pull(
        array &$output,
        &$exitCode
    ): void {
        CBGit::exec(
            'git pull --ff-only',
            $output,
            $exitCode
        );
    }
    /* pull() */



    /**
     * @return [string]
     *
     *      Returns an array of paths relative to the document root containing
     *      submodules.
     */
    static function
    submodules(
    ): array {
        $pwd = getcwd();

        chdir(
            cbsitedir()
        );

        try {
            exec(
                (
                    'git submodule foreach ' .
                    '--recursive --quiet ' .
                    "'echo \$displaypath'"
                ),
                $relativeSubmodulePaths
            );
        } finally {
            chdir($pwd);
        }

        return $relativeSubmodulePaths;
    }
    /* submodules() */



    /**
     * @param array &$output
     * @param mixed &$exitCode
     *
     * @return void
     */
    static function submoduleUpdate(
        &$output,
        &$exitCode
    ): void {

        /**
         * The "git sync" command will synchronize submodules if the remove
         * URL has changed in .gitmodules.
         */

        CBGit::exec(
            'git submodule sync --recursive',
            $output,
            $exitCode
        );

        if (empty($exitCode)) {
            CBGit::exec(
                'git submodule update --init --recursive',
                $output,
                $exitCode
            );
        }
    }
    /* submoduleUpdate() */

}

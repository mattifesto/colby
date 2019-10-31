<?php

final class CBGit {

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

        chdir(cbsitedir());

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
    static function pull(
        array &$output,
        &$exitCode
    ): void {
        CBGit::exec(
            'git pull',
            $output,
            $exitCode
        );
    }



    /**
     * @return [string]
     */
    static function submodules(): array {
        $pwd = getcwd();

        chdir(cbsitedir());

        try {
            exec(
                'git submodule--helper list',
                $submodules
            );
        } finally {
            chdir($pwd);
        }

        return array_map(
            function ($item) {
                $columns = preg_split('/\s/', $item);

                return $columns[3];
            },
            $submodules
        );
    }
    /* submodules() */



    /**
     * @param array &$output
     * @param mixed &$exitCode
     *
     * @return void
     */
    static function submoduleUpdate(&$output, &$exitCode): void {

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

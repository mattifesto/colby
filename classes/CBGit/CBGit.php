<?php

final class CBGit {

    /**
     * @return {
     *  string output
     *  bool wasSuccessful
     * }
     */
    public static function pull() {
        $pwd = getcwd();

        chdir(CBSiteDirectory);

        try {
            exec('git pull 2>&1', $output, $exitcode);
        } finally {
            chdir($pwd);
        }

        return (object)[
            'output' => implode("\n", $output),
            'wasSuccessful' => empty($exitcode),
        ];
    }

    /**
     * @return {
     *  string output
     *  bool wasSuccessful
     * }
     */
    public static function submoduleUpdate() {
        $pwd = getcwd();

        chdir(CBSiteDirectory);

        try {
            exec('git submodule update 2>&1', $output, $exitcode);
        } finally {
            chdir($pwd);
        }

        return (object)[
            'output' => implode("\n", $output),
            'wasSuccessful' => empty($exitcode),
        ];
    }
}

<?php

final class CBGit {

    /**
     * @return {
     *  string output
     *  bool wasSuccessful
     * }
     */
    static function pull() {
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
     * @return [string]
     */
    static function submodules(): array {
        chdir(cbsitedir());
        exec('git submodule--helper list', $submodules);

        return array_map(function ($item) {
            $columns = preg_split('/\s/', $item);
            return $columns[3];
        }, $submodules);
    }

    /**
     * @return {
     *  string output
     *  bool wasSuccessful
     * }
     */
    static function submoduleUpdate() {
        $pwd = getcwd();

        chdir(CBSiteDirectory);

        try {
            exec('git submodule update --init --recursive 2>&1', $output, $exitcode);
        } finally {
            chdir($pwd);
        }

        return (object)[
            'output' => implode("\n", $output),
            'wasSuccessful' => empty($exitcode),
        ];
    }
}

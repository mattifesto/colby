<?php

final class CBExec {

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
        array_push(
            $output,
            "$ {$command}"
        );

        exec(
            "{$command} 2>&1",
            $output2,
            $exitCode
        );

        if (!empty($exitCode)) {
            array_push(
                $output,
                "! returned exit code: {$exitCode}"
            );
        }

        $output = array_merge(
            $output,
            $output2
        );
    }
    /* exec() */

}

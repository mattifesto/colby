<?php

final class
CBTCommand_status {

    /* -- cbt interfaces -- */



    /**
     * @return void
     */
    static function
    cbt_execute(
    ): void {
        echo cbsitedir(), "\n\n";

        $submoduleDirectories = CBGit::submodules();

        array_unshift(
            $submoduleDirectories,
            ''
        );

        foreach ($submoduleDirectories as $submoduleDirectory) {
            CBTCommand_status::displayStatusForDirectory(
                $submoduleDirectory
            );
        }
    }
    /* cbt_execute() */



    /**
     * @param string $directory
     *
     * @return void
     */
    private static function
    displayStatusForDirectory(
        string $directory
    ): void {
        $output = [];
        $submoduleDirectoryDescription = $directory;

        if ($submoduleDirectoryDescription === '') {
            $submoduleDirectoryDescription = 'website';
        }

        echo (
            "-- {$submoduleDirectoryDescription} " .
            str_repeat(
                '-',
                60 - strlen($submoduleDirectoryDescription)
            ) .
            "\n\n"
        );

        chdir(
            cbsitedir() .
            '/' .
            $directory
        );

        exec(
            'git branch',
            $output
        );

        array_push(
            $output,
            ''
        );

        exec(
            'git describe',
            $output
        );

        array_push(
            $output,
            ''
        );

        exec(
            'git status --porcelain',
            $output
        );

        array_push(
            $output,
            ''
        );

        $currentBranch = CBGit::getCurrentBranchName();
        $currentTrackecBranch = CBGit::getCurrentTrackedBranchName();

        if (
            empty($currentBranch) ||
            empty($currentTrackecBranch)
        ) {
            echo "current branch or track branch not valid";
        } else {
            $range = "{$currentTrackecBranch}..{$currentBranch}";

            exec(
                "git log {$range} --oneline --no-decorate --reverse",
                $output
            );
        }

        echo implode(
            "\n",
            $output
        ),
        "\n\n";
    }
    /* displayStatusForDirectory() */

}

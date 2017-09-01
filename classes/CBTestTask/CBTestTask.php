<?php

/**
 * This task exist as an example for how to implement a task.
 */
final class CBTestTask {

    /**
     * @param hex160 $ID
     *
     * @return object
     *
     */
    static function CBTasks2_Execute($ID) {
        return (object)[
            'message' => <<< EOT
CBTestTask, About task messages:

The message on the tasks admin page will be truncated to the first line by default. Clicking on the message will display the entire message so feel free to include any helpful content however long it may be.

White space is preserved.
EOT
            ,
            'severity' => 3,
        ];
    }

    /**
     * @return null
     */
    static function CBAjax_start() {
        CBTasks2::updateTask(__CLASS__, CBHex160::zero(), null, null, time());
    }

    /**
     * @return string
     */
    static function CBAjax_start_group() {
        return 'Administrators';
    }
}

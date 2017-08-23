<?php

/**
 * This task exist as an example for how to implement a task.
 */
final class CBTaskForSample {

    /**
     * @param hex160 $ID
     *
     * @return object
     *
     */
    static function CBTasks2_Execute($ID) {
        return (object)[
            'message' => <<< EOT
The message on the tasks admin page will be truncated to the first line by default. Clicking on the message will display the entire message so feel free to include an necessary content however long it may be.

White space is preserved.
EOT
            ,
            'severity' => 3,
        ];
    }

    static function startForAjax() {
        $response = new CBAjaxResponse();

        CBTasks2::updateTask(__CLASS__, CBHex160::zero(), null, null, time());

        $response->wasSuccessful = true;
        $response->send();
    }

    static function startForAjaxPermissions() {
        return (object)['group' => 'Administrators'];
    }
}

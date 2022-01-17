<?php

/**
 * @deprecated 2022_01_15
 *
 *      This task is deprecated and is left in to handle any remaining scheduled
 *      tasks. It can be removed in verion 676.
 */
final class
CB_Task_GenerateUsername {

    /* -- CBTasks2 interfaces -- */



    /**
     * @param CBID $userModelCBID
     *
     * @return ?object
     */
    static function
    CBTasks2_run(
        string $userModelCBID
    ): ?stdClass {
        return null;
    }
    /* CBTasks2_run() */

}

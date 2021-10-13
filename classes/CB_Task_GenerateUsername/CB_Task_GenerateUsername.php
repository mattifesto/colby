<?php

final class
CB_Task_GenerateUsername {

    /* -- CBTasks2 interfaces -- -- -- -- -- */



    /**
     * @param CBID $userModelCBID
     *
     * @return ?object
     */
    static function
    CBTasks2_run(
        string $userModelCBID
    ): ?stdClass {
        $userModel = CBModels::fetchModelByCBID(
            $userModelCBID
        );


        /**
         * If the user model doesn't exist, complete the task.
         */

        if (
            $userModel === null
        ) {
            return null;
        }


        /**
         * If the model is not a user model, complete the task.
         */

        $userModelClassName = CBModel::getClassName(
            $userModel
        );

        if (
            $userModelClassName !== 'CBUser'
        ) {
            return null;
        }


        /**
         * If the user doesn't have a username, assign them a randomly generated
         * username.
         */

        $currentUsernameCBID = CB_Username::fetchUsernameCBIDByUserCBID(
            $userModelCBID
        );

        if ($currentUsernameCBID === null) {
            $newUsernameSpec = CB_Username::generateRandomUsernameSpec(
                $userModelCBID
            );

            CBDB::transaction(
                function (
                ) use (
                    $newUsernameSpec
                ) {
                    CBModels::save(
                        $newUsernameSpec
                    );
                }
            );
        }

        return null;
    }
    /* CBTasks2_run() */

}

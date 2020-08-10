<?php

final class CBArtworkCollection_UpdatedTask {

    /* -- CBTasks2 interfaces -- -- -- -- -- */



    /**
     * This task will notify the model associated with the updated
     * CBArtworkCollection if the associated model's class has implemented the
     * notification interface.
     *
     * @param CBID $modelCBID
     *
     * @return ?object
     */
    static function CBTasks2_run(
        string $associatedModelCBID
    ): ?stdClass {
        $associatedModel = CBModelCache::fetchModelByID(
            $associatedModelCBID
        );

        $callable = CBModel::getClassFunction(
            $associatedModel,
            'CBArtworkCollection_UpdatedTask_notify'
        );

        if ($callable === null) {
            return null;
        }

        call_user_func(
            $callable,
            $associatedModel
        );

        return null;
    }
    /* CBTasks2_run() */

}

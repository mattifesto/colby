<?php

final class CBModelUpdater {

    /* -- CBInstall interfaces -- -- -- -- -- */



    /**
     * This interface is implemented so that other classes that need to use the
     * CBModelUpdater during install can just add CBModelUpdater to their
     * install required class names rather than figuring out the dependencies.
     *
     * @return [string]
     */
    static function CBInstall_requiredClassNames(): array {
        return [
            'CBModels',
        ];
    }
    /* CBInstall_requiredClassNames() */



    /* -- functions -- -- -- -- -- */



    /**
     * The purpose of this function is to fetch and apply updates to a model.
     * Callers of this function are using it to make even further updates that
     * can't be made with a simple merge of objects.
     *
     * After making additional changes to the returned working spec, save the
     * spec by passing the returned object to the CBModelUpdater::save()
     * function.
     *
     * If further updates to the spec aren't necessary, use the
     * CBModelUpdater::update() function instead.
     *
     * @param object $updates
     *
     *      The object provided must have "className" and "ID" properties. There
     *      are technically situations where specifying the  "className"
     *      property is not necessary, but it is ill advised to depend on them.
     *
     *      This object will be merged with the fetched spec return as the
     *      working spec.
     *
     * @return object
     *
     *      {
     *          original: ?model
     *
     *              The spec as it exists in the database currently. Do not
     *              modify this value.
     *
     *          working: model
     *
     *              The original spec merged with the provided updates object.
     *              Make further modificatons to this object before calling
     *              CBModelUpdater::save().
     *      }
     */
    static function fetch(stdClass $updates): stdClass {
        $ID = CBModel::valueAsID($updates, 'ID');
        $originalSpec = CBModels::fetchSpecByIDNullable($ID);

        if (empty($originalSpec)) {
            $workingSpec = CBModel::clone($updates);
        } else {
            $workingSpec = CBModel::clone($originalSpec);

            CBModel::merge($workingSpec, $updates);
        }

        return (object)[
            'original' => $originalSpec,
            'working' => $workingSpec,
        ];
    }
    /* fetch() */



    /**
     * @param object $updater
     *
     * @return void
     */
    static function save(stdClass $updater): void {
        if ($updater->working != $updater->original) {
            CBDB::transaction(
                function () use ($updater) {
                    CBModels::save($updater->working);
                }
            );
        }
    }
    /* save() */



    /**
     * This function will create or updated the target model.
     *
     * @param object $updates
     *
     * @return void
     */
    static function update(
        stdClass $updates
    ): void {
        $updater = CBModelUpdater::fetch($updates);

        CBModelUpdater::save($updater);
    }
    /* update() */



    /**
     * This function will only update the target model if it already exists.
     *
     * @param object $updates
     *
     * @return void
     */
    static function updateIfExists(
        stdClass $updates
    ): void {
        $updater = CBModelUpdater::fetch($updates);

        if ($updater->original !== null) {
            CBModelUpdater::save($updater);
        }
    }
    /* updateIfExists() */

}

<?php

/**
 * See documentation.
 *
 * @NOTE 2012_02_15
 *
 *      This class is in transition from using primarily static functions to an
 *      object-oriented from. The static methods will soon be deprecated after
 *      a trial period.
 */
final class CBModelUpdater {

    private $originalSpec;
    private $workingSpec;



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
     * @param string $CBID
     */
    function
    __construct(
        string $CBID
    ) {
        $this->workingSpec = null;

        $this->originalSpec = CBModels::fetchSpecByCBID(
            $CBID
        );

        if ($this->originalSpec === null) {
            $this->workingSpec = (object)[];

            CBModel::setCBID(
                $this->workingSpec,
                $CBID
            );
        } else {
            $this->workingSpec = CBModel::clone(
                $this->originalSpec
            );
        }
    }
    /* __construct() */



    /**
     * @return object
     */
    function
    getSpec(
    ): stdClass {
        return $this->workingSpec;
    }
    /* getSpec() */



    /**
     * @TODO 2021_02_15
     *
     *      This method will be renamed to "save" one the static "save" function
     *      is fully deprecated and removed.
     *
     * @return void
     */
    function
    save2(
    ): void {
        if ($this->workingSpec != $this->originalSpec) {
            CBModels::save(
                $this->workingSpec
            );

            $version = CBModel::getVersion(
                $this->workingSpec
            );

            CBModel::setVersion(
                $this->workingSpec,
                $version + 1,
            );

            $this->originalSpec = CBModel::clone(
                $this->workingSpec
            );
        }
    }
    /* save() */



    /**
     * @deprecated 2021_01_15
     *
     *      Change code to use this class in an object-oriented manner.
     *
     *      This method is deprecated because it forces you to use the 'ID'
     *      property directly. The 'ID' property has been deprecated and
     *      replaced by the CBModel::get|setCBID() accessors.
     *
     *      This function also promotes the use of direct property access which
     *      is not safe in the context of general use cases for the function.
     *
     *      Use CBModelUpdater::fetchByCBID().
     *
     *
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
     *          original: object|null
     *
     *              The spec as it exists in the database currently. Do not
     *              modify this value.
     *
     *          working: object
     *
     *              The original spec merged with the provided updates object.
     *              Make further modificatons to this object before calling
     *              CBModelUpdater::save().
     *      }
     */
    static function
    fetch(
        stdClass $updates
    ): stdClass {
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
     * @deprecated 2021_02_15
     *
     *      Change code to use this class in an object-oriented manner.
     *
     * @param CBID $CBID
     *
     * @return object
     *
     *      {
     *          CBModelUpdater_getSpec() -> object
     *
     *              If the model exists a clone of its spec will be returned. If
     *              the model does not exist, a spec with only its CBID property
     *              set will be returned.
     *
     *          CBModelUpdater_save() -> void
     *      }
     */
    static function fetchByCBID(
        string $CBID
    ): stdClass {
        $workingSpec = null;

        $originalSpec = CBModels::fetchSpecByCBID(
            $CBID
        );

        if ($originalSpec === null) {
            $workingSpec = (object)[];

            CBModel::setCBID(
                $workingSpec,
                $CBID
            );
        } else {
            $workingSpec = CBModel::clone(
                $originalSpec
            );
        }



        /**
         * @return object
         */
        $CBModelUpdater_getSpec = function() use (
            $workingSpec
        ): stdClass {
            return $workingSpec;
        };
        /* CBModelUpdater_getSpec() */



        /**
         * @return void
         */
        $CBModelUpdater_save = function() use (
            $originalSpec,
            $workingSpec
        ): void {
            if ($workingSpec != $originalSpec) {
                CBModels::save(
                    $workingSpec
                );
            }
        };
        /* CBModelUpdater_save() */



        return (object)[
            'CBModelUpdater_getSpec' => $CBModelUpdater_getSpec,
            'CBModelUpdater_save' => $CBModelUpdater_save,
        ];
    }
    /* fetchByCBID() */



    /**
     * @deprecated 2012_02_15
     *
     *      Change code to use this class in an object-oriented manner.
     *
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
     * @deprecated 2012_02_15
     *
     *      Change code to use this class in an object-oriented manner.
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
     * @TODO 2012_02_15
     *
     *      This is an interesting function. I'm not entirely sure at this
     *      moment how to accomplish it the right way. It's core problem is
     *      that it accepts updates as a partial spec.
     *
     *      This may not be so bad in some ways if property names aren't
     *      referenced and the function becomes more like "merge if exists".
     *
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

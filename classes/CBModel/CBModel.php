<?php

/**
 * A model is an data storage object with its className property set to a
 * non-empty string. This class name indicates which class the model's data is
 * associated with.
 *
 * In object-oriented programming data lives inside class instances. Colby takes
 * a more functional approach and data lives inside models. Models don't
 * represent instances of classes, they are just data. The class has functions
 * that use or process this data in different ways the class creator has
 * determined are useful.
 *
 * Classes often build one model from another model by implementing the
 * CBModel_build() interface which is called by the CBModel::build()
 * function. In the context of buiding models, we refer to the source model as a
 * spec. The spec is still a model, but the word spec is used in this context to
 * indicate that this model is the source model.
 *
 * As an example, classes implementing the CBView_render() interface use model
 * data to render HTML content. Those classes usually also use model data to
 * generate search text and build models from specs.
 *
 * Array Function Safety
 *
 * The functions of CBModel are designed to be safe for array functions. They
 * take a mixed-type first parameter and return a nullable value. In most cases
 * the parameter is expected to be a model, if it is not a model, null is
 * returned from the function.
 *
 * This enables the functions to be used with functions like array_map() without
 * having to verify beforehand that every item in the array is a model. Adding a
 * call to array_filter() will remove the null values so these functions work to
 * remove non-model values from arrays.
 *
 * This self-cleaning behavior is better than a strongly typed behavior because
 * while a notification of a non-model value might be nice, that notification
 * would come in the form of an exception and the process of later removing a
 * non-model value would be difficult. Non-model values in this context will not
 * be a common situation.
 */
final class
CBModel {

    private static $rootSpecCurrentlyBeingUpgraded = null;



    /* -- CBHTMLOutput interfaces -- -- -- -- -- */



    /**
     * @return [string]
     */
    static function
    CBHTMLOutput_JavaScriptURLs(
    ): array {
        return [
            Colby::flexpath(
                __CLASS__,
                'v675.48.js',
                cbsysurl()
            ),
        ];
    }
    /* CBHTMLOutput_JavaScriptURLs() */



    /**
     * @return [string]
     */
    static function
    CBHTMLOutput_requiredClassNames(
    ): array {
        return [
            'CBConvert',
            'CBException',
            'CBID',
        ];
    }
    /* CBHTMLOutput_requiredClassNames() */



    /* -- accessors -- */



    /**
     * @param object $model
     *
     * @return CBID|null
     */
    static function
    getCBID(
        stdClass $model
    ): ?string {
        return CBModel::valueAsCBID(
            $model,
            'ID'
        );
    }
    /* getCBID() */



    /**
     * @param object $spec
     * @param CBID $CBID
     *
     * @return void
     */
    static function
    setCBID(
        stdClass $spec,
        string $CBID
    ): void {
        if (!CBID::valueIsCBID($CBID)) {
            throw new CBExceptionWithValue(
                'The CBID provided is not valid.',
                $CBID,
                '05f35925a3654b8fd52f0df5c4b6f6e1e0adf6e4'
            );
        }

        $spec->ID = $CBID;
    }
    /* setCBID() */



    /**
     * @param object $model
     *
     * @return string
     */
    static function
    getClassName(
        stdClass $model
    ): string {
        return CBModel::valueToString(
            $model,
            'className'
        );
    }
    /* getClassName() */



    /**
     * @param object $spec
     * @param string $className
     *
     * @return void
     */
    static function
    setClassName(
        stdClass $spec,
        string $className
    ): void {
        $classNameAsName = CBConvert::valueAsName(
            $className
        );

        if ($classNameAsName === null) {
            throw new CBExceptionWithValue(
                'The class name must be a valid Colby name value.',
                $className,
                '7f5a08b9d4d548d3e6972a2e683929f3e4877f73'
            );
        }

        $spec->className = $className;
    }
    /* setClassName() */



    /**
     * @param object $model
     *
     * @return int
     *
     *      This function returns an integer greater than or equal to 0. A
     *      version of 0 means this is a spec that has never been saved.
     */
    static function
    getVersion(
        stdClass $model
    ): int {
        return CBModel::valueAsInt(
            $model,
            'version'
        ) ?? 0;
    }
    /* getVersion() */



    /**
     * @param object $spec
     * @param int $version
     *
     *      The value of this parameter must be greater than or equal to 0.
     *
     * @return void
     */
    static function
    setVersion(
        stdClass $spec,
        int $version
    ): void {
        if ($version < 0) {
            throw new CBExceptionWithValue(
                'The version must be greater than or equal to zero.',
                $version,
                '3dab363ac045c3568fb66f574eb7d31ef2079de1'
            );
        }

        $spec->version = $version;
    }
    /* setVersion() */



    /* -- functions -- */



    /**
     * @param string $className
     *
     * @return bool
     *
     *      Returns true if the class is a model class.
     */
    static function
    classIsModel(
        string $className
    ): bool {
        $functionName = "{$className}::CBModel_build";

        return is_callable(
            $functionName
        );
    }
    /* classIsModel() */



    /**
     * This function returns a copy of the $spec parameter.
     *
     * Copies of model specs are further processed by model classes that
     * implement the CBModel_prepareCopy() interface. The interface will be
     * passed a copy of the spec. The interface can return that object with or
     * without modifications, or it can return an entirely different object. The
     * interface must return a valid model.
     *
     * The copy preparation usually involves resetting properties that need to
     * be reset for copied specs. For instance, a copied page spec will be
     * unpublished.
     *
     * @param model $spec
     *
     *      The spec to copy.
     *
     * @param CBID $newSpecCBID
     *
     *      The ID for the copy.
     *
     * @return ?model
     *
     *      If the $spec parameter is not a model, this function will return
     *      null. Otherwise, this function will always return another model.
     *
     *      The returned model will always be a different object than $spec
     *      argument.
     */
    static function
    copy(
        stdClass $spec,
        string $newSpecCBID
    ): ?stdClass {
        $spec = CBConvert::valueAsModel(
            $spec
        );

        if (
            $spec === null
        ) {
            return null;
        }

        $newSpec = CBModel::clone(
            $spec
        );

        CBModel::setCBID(
            $newSpec,
            $newSpecCBID
        );

        unset(
            $newSpec->version
        );

        /**
         * @deprecated 2021_01_15
         *
         *      This function is acting as if the "title" property is an
         *      official property of all models. It was at one time but no
         *      longer is, the "title" property should be handled by classes
         *      that implement the CBModel_prepareCopy() interface and support a
         *      "title" property.
         *
         *      Potentially deprecate the CBModel_prepareCopy() interface and
         *      replace it with the CBModel_prepareCopy2() interface to enforce
         *      this change.
         */

        $title = trim(
            CBModel::valueToString(
                $spec,
                'title'
            )
        );

        $newSpec->title = (
            empty($title) ?
            'Copy' :
            "{$title} Copy"
        );

        $className = CBModel::getClassName(
            $spec
        );

        $functionName = "{$className}::CBModel_prepareCopy";

        if (
            is_callable(
                $functionName
            )
        ) {
            $newSpec = call_user_func(
                $functionName,
                $newSpec
            );
        }

        return $newSpec;
    }
    /* copy() */



    /**
     * This function performs a deep clone of a model by serializing it to JSON
     * and then unserializing it. Since models are always serialized to JSON
     * this method of cloning is will always produce a valid clone.
     *
     * @param mixed $model
     *
     *      This parameter does not technically have to be a model. It does need
     *      to be serializable to JSON.
     *
     * @return mixed
     */
    static function
    clone(
        $model
    ) {
        return json_decode(
            json_encode(
                $model
            )
        );
    }
    /* clone() */



    /**
     * @param string|null $className
     * @param CBID|null $CBID
     */
    static function
    createSpec(
        ?string $className = null,
        ?string $CBID = null
    ): stdClass {
        $spec = (object)[];

        if ($className !== null) {
            CBModel::setClassName(
                $spec,
                $className
            );
        }

        if ($CBID !== null) {
            CBModel::setCBID(
                $spec,
                $CBID
            );
        }

        return $spec;
    }
    /* createSpec() */



    /**
     * Returns the the first model found with the specified property value.
     *
     * @param [object] $models
     * @param string $propertyName
     * @param mixed $propertyValue
     *
     *      The property value is compared to the model property value with the
     *      == operator.
     *
     * @return ?object
     *
     *      Returns the found model or null if no match is found.
     */
    static function findModelInArrayByPropertyValue(
        array $models,
        string $propertyName,
        $propertyValue
    ): ?stdClass {
        foreach ($models as $model) {
            $value = CBModel::value($model, $propertyName);

            if ($value == $propertyValue) {
                return $model;
            }
        }

        return null;
    }
    /* findModelInArrayByPropertyValue() */



    /**
     * @param object $model
     * @param string $functionName
     *
     * @return callable|null
     */
    static function
    getClassFunction(
        stdClass $model,
        string $functionName
    ): ?callable {
        $className = CBModel::getClassName(
            $model,
        );

        return CBConvert::classNameAndFunctionNameToCallable(
            $className,
            $functionName
        );
    }
    /* getClassFunction() */



    /**
     * @param object $model
     *
     * @return [<CB_Timestamp>]
     *
     *      An array of cbtimestamps that this model uses. These cbtimestamps
     *      should have been reserved when they were created.
     */
    static function
    getCBTimestamps(
        stdClass $model
    ): array {
        $callable = CBModel::getClassFunction(
            $model,
            'CBModel_getCBTimestamps'
        );

        if (
            $callable === null
        ) {
            return [];
        } else {
            return call_user_func(
                $callable,
                $model
            );
        }
    }
    /* getCBTimestamps() */



    /**
     * @return object|null
     *
     *      When a model upgrade is in progress, a clone of the root model that
     *      is currenlty being upgraded is returned from this function;
     *      otherwise null will be returned.
     */
    static function
    getRootSpecCurrentlyBeingUpgraded(
    ): ?stdClass {
        return CBModel::clone(
            CBModel::$rootSpecCurrentlyBeingUpgraded
        );
    }
    /* getRootSpecCurrentlyBeingUpgraded() */



    /**
     * @param object $model
     *
     * @return string
     */
    static function
    getTitle(
        stdClass $model
    ): string {
        $callable = CBModel::getClassFunction(
            $model,
            'CBModel_getTitle'
        );

        if (
            $callable !== null
        ) {
            return call_user_func(
                $callable,
                $model
            );
        } else {
            return '';
        }
    }
    /* getTitle() */



    /**
     * Returns the index of the first model found with the specified property
     * value.
     *
     * @param [object] $models
     * @param string $propertyName
     * @param mixed $propertyValue
     *
     *      The property value is compared to the model property value with the
     *      == operator.
     *
     * @return mixed
     *
     *      Returns an int for numeric arrays and a string for associative
     *      arrays. Returns null if no match is found.
     */
    static function indexOf(
        array $models,
        string $propertyName,
        $propertyValue
    ) {
        foreach ($models as $index => $model) {
            $value = CBModel::value($model, $propertyName);

            if ($value == $propertyValue) {
                return $index;
            }
        }

        return null;
    }



    /**
     * This function can be used to generate IDs for specs, such as specs
     * imported from CSV files.
     *
     * @param mixed $spec
     *
     *      This function takes a mixed parameter to make it "array function
     *      safe".
     *
     * @return string
     */
    static function toID($spec): string {
        $className = CBModel::valueAsName($spec, 'className');

        if ($className === null) {
            throw CBException::createModelIssueException(
                'An ID can\'t be generated for this spec because the ' .
                'spec has an invalid "className" property value.',
                $spec,
                'b4822d4eda69523f65029f047c2039e02196119d'
            );
        }

        if (!class_exists($className)) {
            throw CBException::createModelIssueException(
                'An ID can\'t be generated for this spec because a class ' .
                "with the name \"{$className}\" doesn't exist.",
                $spec,
                'a8479ae50d410cc8b3f572402dddf2a047fa5020'
            );
        }

        $functionName = "{$className}::CBModel_toID";

        if (!is_callable($functionName)) {
            throw CBException::createModelIssueException(
                'An ID can\'t be generated for this spec because ' .
                "the CBModel_toID() interface has not been implemented " .
                "on the {$className} class.",
                $spec,
                'eda34362fa7ff5a25850a99b95877a75e018447e'
            );
        }

        $ID = call_user_func($functionName, $spec);

        if (CBConvert::valueAsID($ID) === null) {
            throw CBException::createModelIssueException(
                "An ID can't be generated for this spec because " .
                "the {$functionName}() interface returned a value that is " .
                "not an ID.",
                $spec,
                'bdd815415de119b95545340af3a0643f87ea31e9'
            );
        }

        return $ID;
    }
    /* toID() */



    /**
     * This function builds a new model from a source model. The source model,
     * in this context, is called a spec. Models are built to provide an
     * opportunity to process a user edited spec into a more stable or
     * performant model. For instance, the building function may precompute the
     * HTML escaped versions of strings.
     *
     * Reading this function will help developers understand the exact
     * interactions and requirements of specs and models.
     *
     *      @NOTE
     *      A model will always have the same class name as its spec.
     *
     *      In the past a spec was allowed to generate a model with a different
     *      class name, but this was never used. Allowing this to occur presents
     *      the dilemma of which class name should be used in the CBModels
     *      table. Model transitions may need to take place, but the transition
     *      should not occur during CBModel::build().
     *
     *      @NOTE
     *      If a model needs an "ID" property value, for example because it's
     *      going to be saved, the "ID" property must be set on the spec. If a
     *      spec "ID" property value is set it must be a valid CBID and will
     *      always be copied to the model.
     *
     *      Generated CBIDs:
     *
     *      Sometimes specs are imported from CSV files created from
     *      spreadsheets edited by website administrators. Those users may
     *      specify human readable unique identifiers like product codes instead
     *      of CBIDs. The import process will notice that a spec doesn't have an
     *      "ID" property value and will call CBModel::toID() to generate a CBID
     *      which it will then set as the "ID" property value on the spec.
     *
     *      Any CBID generation must be done for a spec before it is used with
     *      this function.
     *
     *      @deprecated 2018_12_21 (edited 2021_01_15)
     *
     *          This function is setting the "title" property on the model even
     *          though the "title" property is not an official property of all
     *          models. To resolve this we may need to replace the interface
     *          CBModel_build() with CBModel_build2(). This sound drastic, but
     *          this is exactly how this exact type of scenario should be
     *          resolved.
     *
     * @param object $spec
     *
     *      For this function to successfully build a model, the spec's class
     *      must exist and have the CBModel_build() interface implemented.
     *
     * @return object
     *
     *      The build process is allowed to have requirements and allowed to
     *      throw exceptions if those requirements are not met.
     */
    static function
    build(
        stdClass $spec
    ): stdClass {
        static $staticRootSpec = null;

        try {
            if ($staticRootSpec === null) {
                $staticRootSpec = $spec;
            }

            $className = CBModel::valueAsName(
                $spec,
                'className'
            );

            if ($className === null) {
                throw new CBExceptionWithValue(
                    (
                        'This spec can\'t be built because it has an invalid ' .
                        '"className" property value.'
                    ),
                    $spec,
                    'd24a83a81c914e1a5b66eeede05a577c0c44bd57'
                );
            }

            if (!class_exists($className)) {
                throw new CBExceptionWithValue(
                    (
                        'This spec can\'t be built because a class ' .
                        "with the name \"{$className}\" doesn't exist."
                    ),
                    $spec,
                    '0f170c152f54ebb97ecd2fb0a27055a096276d37'
                );
            }

            $buildInterfaceFunctionName = "{$className}::CBModel_build";

            if (
                is_callable(
                    $buildInterfaceFunctionName
                )
            ) {
                $model = call_user_func(
                    $buildInterfaceFunctionName,
                    $spec
                );
            } else {
                throw new CBExceptionWithValue(
                    CBConvert::stringToCleanLine(<<<EOT

                        This spec can't be built because the CBModel_build()
                        interface has not been implemented by the {$className}
                        class.

                    EOT),
                    $spec,
                    'a92922e1bcf4fe374b54a2b45bd59403f2214faa'
                );
            }

            if (
                !is_object(
                    $model
                )
            ) {
                throw new CBExceptionWithValue(
                    CBConvert::stringToCleanLine(<<<EOT

                        This spec can't be built because the CBModel_build()
                        interface returned a value that is not an object.

                    EOT),
                    $spec,
                    '2a8ad1dd8a2d47a80d41609b98056f0e8775a47a'
                );
            }

            /**
             * Since "className" is the one required property for a model and
             * should always be transferred by the build process, this function
             * is allowed to transfer the property for all models.
             */
            $model->className = $className;


            /**
             * A model will always have the same CBID as its spec. A malformed
             * CBID on the spec will cause an exception to be thrown.
             */
            $ID = CBModel::valueAsID($spec, 'ID');

            if (isset($spec->ID) && $ID === null) {
                throw new CBExceptionWithValue(
                    (
                        'This spec can\'t be built because it has an invalid ' .
                        '"ID" property value.'
                    ),
                    $spec,
                    '11759b8ba7d8ae54039371942c9b09e29cda59d6'
                );
            }

            if ($ID !== null) {
                $model->ID = $ID;
            }

            /**
             * @deprecated 2018_12_21
             *
             *      This code is deprecated. It infers that if the spec has its
             *      "title" property set that the "title" property is a valid
             *      string property for this particular model. In the future the
             *      class will be responsible for building all model properties.
             */
            if (!isset($model->title) && isset($spec->title)) {
                $model->title = trim(
                    CBModel::valueToString($spec, 'title')
                );
            }
        } catch (Throwable $throwable) {
            if ($staticRootSpec !== $spec) {
                throw new CBExceptionWithValue(
                    'An exception was thrown while building this root spec.',
                    $staticRootSpec,
                    'f917e127c1f972ce7c7b7b589997366403ec0ce9',
                    0,
                    $throwable
                );
            } else {
                throw $throwable;
            }
        } finally {
            if ($staticRootSpec === $spec) {
                $staticRootSpec = null;
            }
        }


        /* done */

        return $model;
    }
    /* build() */



    /**
     * Transfer the first level property values of object 2 onto object 1.
     * Object 1 may be altered, object 2 will not be altered.
     *
     * This function is meant to be used with models, but there is no reason it
     * can't be used with objects that aren't technically models.
     *
     * @param object $object1
     * @param object $object2
     *
     * @return void
     */
    static function merge(stdClass $object1, stdClass $object2): void {
        foreach ($object2 as $key => $value) {
            $object1->{$key} = $value;
        }
    }
    /* merge() */



    /**
     * @param mixed $model
     *
     *      This function takes a mixed parameter to make it "array function
     *      safe".
     *
     * @return string
     */
    static function
    toSearchText(
        $model
    ): string {
        $className = CBModel::getClassName(
            $model,
        );

        if (
            empty($className)
        ) {
            return '';
        }

        $text = '';

        if (
            is_callable(
                $function = "{$className}::CBModel_toSearchText"
            )
        ) {
            $text = call_user_func(
                $function,
                CBModel::clone(
                    $model
                )
            );
        }

        /* deprecated */
        else if (
            is_callable(
                $function = "{$className}::modelToSearchText"
            )
        ) {
            $text = call_user_func(
                $function,
                CBModel::clone(
                    $model
                )
            );
        }

        $CBID = CBModel::getCBID(
            $model
        );

        return implode(
            ' ',
            array_filter(
                [
                    $text,
                    $CBID,
                ]
            )
        );
    }
    /* toSearchText() */



    /**
     * @param mixed $model
     *
     * @return string
     */
    static function
    toURLPath(
        $model
    ) {
        $className = CBModel::getClassName(
            $model,
        );

        if (
            empty($className)
        ) {
            return '';
        }

        $functionName = "{$className}::CBModel_toURLPath";

        if (
            is_callable(
                $functionName
            )
        ) {
            $potentialURLPath = call_user_func(
                $functionName,
                CBModel::clone(
                    $model
                )
            );

            if (
                $potentialURLPath === ''
            ) {
                return '';
            }

            $isValid =
            CB_URL::potentialURLPathIsValid(
                $potentialURLPath
            );
            
            if (
                $isValid !== true
            ) {
                throw new CBExceptionWithValue(
                    CBConvert::stringToCleanLine(<<<EOT

                        The class for this model returned the value of
                        "{$potentialURLPath}" from the function
                        CBModel_toURLPath(). This value is not a valid URL path.

                    EOT),
                    $model,
                    'af0223135f24cee34d999ec5e6cbf13f9bc1aa94'
                );
            }

            return $potentialURLPath;
        }

        return '';
    }
    /* toURLPath() */



    /**
     * This function returns an upgraded version of the $originalSpec parameter.
     *
     * Model specs are upgraded by model classes that implement the
     * CBModel_upgrade() interface. The interface will be passed a clone of the
     * spec to be upgraded. The interface can return that object with or without
     * modifications, or it can return an entirely different object. The
     * interface must return a valid model.
     *
     * @param mixed $originalSpec
     *
     *      This function takes a mixed parameter to make it "array function
     *      safe".
     *
     * @return object
     *
     *      The returned model will always be a different object than
     *      $originalSpec argument. However the returned model may be the same
     *      as the $originalSpec argument. You can compare the $originalSpec
     *      argument to the returned model using == to determine if any changes
     *      were made during the upgrade.
     */
    static function
    upgrade(
        $originalSpec
    ): stdClass {
        if (
            CBConvert::valueAsModel(
                $originalSpec
            ) === null
        ) {
            throw CBException::createModelIssueException(
                CBConvert::stringToCleanLine(<<<EOT

                    The value of the 'originalSpec' argument can't be upgraded
                    because it is not a model.

                EOT),
                $originalSpec,
                'a38964f4fd545b2c8f568808d5f3035b168c6fc9'
            );
        }

        if (
            CBModel::$rootSpecCurrentlyBeingUpgraded === null
        ) {
            CBModel::$rootSpecCurrentlyBeingUpgraded = $originalSpec;
        }

        $originalCBID = CBModel::valueAsCBID(
            $originalSpec,
            'ID'
        );


        /**
         * @deprecated 2022_01_09
         *
         *      Use CBModel::getRootSpecCurrentlyBeingUpgraded() instead of
         *      CBID functions. This code is attempting to let the upgrade
         *      process of submodels know the CBID of the root model, but it
         *      breaks in two places.
         *
         *      1. If the root model does not have a CBID then the submodels
         *      will get null or even possibly a different CBID that had been
         *      pushed before upgrade had ever been called.
         *
         *      2. If a submodel actually has a CBID then its submodels will get
         *      its CBID instead of the root model CBID.
         */

        {
            if (
                !empty(
                    $originalCBID
                )
            ) {
                CBID::push(
                    $originalCBID
                );
            }
        }

        try {
            $upgradedSpec = CBModel::clone(
                $originalSpec
            );

            $callable = CBModel::getClassFunction(
                $originalSpec,
                'CBModel_upgrade'
            );


            if (
                $callable !== null
            ) {
                $upgradedSpec = call_user_func(
                    $callable,
                    $upgradedSpec
                );

                /* verify upgraded spec */

                if (
                    CBConvert::valueAsModel(
                        $upgradedSpec
                    ) === null
                ) {
                    throw new Exception(
                        "{$function}() returned an invalid model"
                    );
                }

                $upgradedCBID = CBModel::valueAsCBID(
                    $upgradedSpec,
                    'ID'
                );

                if (
                    $upgradedCBID != $originalCBID
                ) {
                    $value = (object)[
                        'originalSpec' => $originalSpec,
                        'upgradedSpec' => $upgradedSpec,
                    ];

                    throw CBException::createModelIssueException(
                        (
                            'The upgraded spec has a different ID than the ' .
                            'original spec.'
                        ),
                        $value,
                        '88c7bd6b23e18073302ef354def22d7f1f101e66'
                    );
                }
            }

            /**
             * @NOTE 2022_01_15
             *
             *      The CBModel_versionDate_property value is only updated on
             *      root specs when every single model needs to be re-processed.
             *
             * 2022_01_15 CBModel::toSearchText() no longer adds the model
             * class name to the search text.
             **/

             if (
                 CBModel::$rootSpecCurrentlyBeingUpgraded === $originalSpec
             ) {
                 $upgradedSpec->CBModel_versionDate_property = '2022_01_15';
             }
        } finally {
            {
                /**
                 * @deprecated 2022_01_09
                 *
                 *      See comment above.
                 */

                if (
                    !empty(
                        $originalCBID
                    )
                ) {
                    CBID::pop();
                }
            }

            if (
                CBModel::$rootSpecCurrentlyBeingUpgraded === $originalSpec
            ) {
                CBModel::$rootSpecCurrentlyBeingUpgraded = null;
            }
        }

        return $upgradedSpec;
    }
    /* upgrade() */



    /**
     * @param mixed $originalValue
     *
     *      This function takes a mixed parameter to make it "array function
     *      safe" and also because it supports arrays for the original value and
     *      array property values.
     *
     *      Any value for this parameter that is not a stdClass or an array is
     *      guarateed to return the default value.
     *
     *      This behavior reduces the amount of validation code required in many
     *      cases. For instance, it allows code to fetch a model and not
     *      validate that the model exists (the model value may be `false` in
     *      this case) before checking to see if a value is set.
     *
     * @param string $keyPath
     *
     *      Examples: "height", "width", "image.height",
     *                "images.[2].alternativeText.text"
     *
     * @param mixed $defaultValue (deprecated)
     * @param function $transform (deprecated)
     *
     * @return mixed
     */
    static function
    value(
        $originalValue,
        $keyPath,
        $defaultValue = null,
        callable $transform = null
    ) {
        $value = $originalValue;

        $keys = explode(
            '.',
            $keyPath
        );

        foreach (
            $keys as $key
        ) {
            if ($key === '') {
                return $defaultValue;
            }

            /* array index */

            if (
                mb_substr($key, 0, 1) === '['
            ) {
                if (
                    !is_array($value)
                ) {
                    return $defaultValue;
                }

                $result = preg_match(
                    '/^\[([0-9]+)\]$/',
                    $key,
                    $matches
                );

                if ($result !== 1) {
                    return $defaultValue;
                }

                $arrayIndex = $matches[1];

                if (
                    isset($value[$arrayIndex])
                ) {
                    $value = $value[$arrayIndex];

                    continue;
                } else {
                    return $defaultValue;
                }
            }


            /* object property */

            if (
                isset($value->{$key})
            ) {
                $value = $value->{$key};

                continue;
            }


            /* default */

            return $defaultValue;
        }
        /* foreach */

        if ($transform !== null) {
            $value = call_user_func(
                $transform,
                $value
            );
        }

        return $value;
    }
    /* value() */



    /**
     * @param mixed $model
     * @param string $keyPath
     *
     * @return array|null
     */
    static function
    valueAsArray(
        /* mixed */ $model,
        string $keyPath
    ): ?array {
        return CBConvert::valueAsArray(
            CBModel::value(
                $model,
                $keyPath
            )
        );
    }
    /* valueAsArray() */



    /**
     * @param mixed $model
     * @param string $keyPath
     *
     * @return CBID|null
     */
    static function valueAsCBID($model, string $keyPath): ?string {
        $value = CBModel::value($model, $keyPath);

        if (CBID::valueIsCBID($value)) {
            return $value;
        } else {
            return null;
        }
    }



    /**
     * @param mixed $model
     * @param string $keyPath
     *
     * @return string|null
     */
    static function valueAsEmail(
        /* mixed */ $model,
        string $keyPath
    ): ?string {
        return CBConvert::valueAsEmail(
            CBModel::value($model, $keyPath)
        );
    }



    /**
     * @deprecated use CBModel::valueAsCBID()
     */
    static function valueAsID($model, string $keyPath): ?string {
        return CBModel::valueAsCBID($model, $keyPath);
    }



    /**
     * This is a convenience function for:
     *
     *      CBConvert::valueAsInt(CBModel::value(...))
     *
     * @param mixed $model
     * @param string $keyPath
     *
     * @return ?int
     */
    static function valueAsInt($model, string $keyPath): ?int {
        return CBConvert::valueAsInt(
            CBModel::value($model, $keyPath)
        );
    }



    /**
     * This is a convenience function for:
     *
     *      CBConvert::valueAsModel(CBModel::value(...))
     *
     * @param mixed $model
     * @param string $keyPath
     * @param string|[string] $classNames
     *
     * @return object|null
     */
    static function
    valueAsModel(
        $model,
        string $keyPath,
        $classNames = []
    ): ?stdClass {
        $value = CBModel::value(
            $model,
            $keyPath
        );

        return CBConvert::valueAsModel(
            $value,
            $classNames
        );
    }
    /* valueAsModel() */



    /**
     * @deprecated use CBModel::valueAsName()
     *
     * @param mixed $model
     * @param string $keyPath
     *
     * @return ?string
     */
    static function valueAsMoniker($model, string $keyPath): ?string {
        return CBConvert::valueAsMoniker(
            CBModel::value($model, $keyPath)
        );
    }



    /**
     * @param mixed $model
     * @param string $keyPath
     *
     * @return ?string
     */
    static function valueAsName($model, string $keyPath): ?string {
        return CBConvert::valueAsName(
            CBModel::value($model, $keyPath)
        );
    }



    /**
     * @param mixed $model
     * @param string $keyPath
     *
     * @return [string]|null
     *
     *      If the $value paramater contains only whitespace or comma separated
     *      whitespace an empty array is returned.
     *
     *      If the $value parameter contains a valid set of names, an array of
     *      names is returned.
     *
     *      If the $value parameter contains any characters not allowed in a
     *      name, null will be returned.
     */
    static function valueAsNames(
        $model,
        string $keyPath
    ): ?array {
        return CBConvert::valueAsNames(
            CBModel::value($model, $keyPath)
        );
    }
    /* valueAsNames() */



    /**
     * This is a convenience function for:
     *
     *      CBConvert::valueAsNumber(CBModel::value(...))
     *
     * @param mixed $model
     * @param string $keyPath
     *
     * @return ?float
     */
    static function valueAsNumber($model, string $keyPath): ?float {
        return CBConvert::valueAsNumber(
            CBModel::value($model, $keyPath)
        );
    }



    /**
     * This is a convenience function for:
     *
     *      CBConvert::valueAsObject(CBModel::value(...));
     *
     * @param mixed $model
     * @param string $keyPath
     *
     * @return ?object
     */
    static function valueAsObject($model, string $keyPath): ?stdClass {
        return CBConvert::valueAsObject(
            CBModel::value($model, $keyPath)
        );
    }



    /**
     * This is a convenience function for:
     *
     *      CBConvert::valueToArray(CBModel::value(...))
     *
     * @param mixed $model
     * @param string $keyPath
     *
     * @return [mixed]
     */
    static function valueToArray($model, string $keyPath): array {
        return CBConvert::valueToArray(
            CBModel::value($model, $keyPath)
        );
    }



    /**
     * This is a convenience function for:
     *
     *      CBConvert::valueToBool(CBModel::value(...))
     *
     * CBConvert::valueToBool() behaves differently than boolval() when the
     * value is a string.
     *
     * @param mixed $model
     * @param string $keyPath
     *
     * @return bool
     */
    static function valueToBool($model, string $keyPath): bool {
        return CBConvert::valueToBool(
            CBModel::value($model, $keyPath)
        );
    }



    /**
     * @param mixed $model
     * @param string $keyPath
     *
     * @return [string]
     */
    static function valueToCommaSeparatedValues(
        $model,
        string $keyPath
    ): array {
        return CBConvert::valueToCommaSeparatedValues(
            CBModel::value($model, $keyPath)
        );
    }



    /**
     * This is a convenience function for:
     *
     *      CBConvert::valueToObject(CBModel::value(...));
     *
     * @param mixed $model
     * @param string $keyPath
     *
     * @return object
     */
    static function valueToObject($model, string $keyPath): stdClass {
        return CBConvert::valueToObject(
            CBModel::value($model, $keyPath)
        );
    }



    /**
     * @deprecated use CBModel::valueAsNames()
     *
     * This is a convenience function for:
     *
     *      CBConvert::valueToNames(CBModel::value(...));
     *
     * @param mixed $model
     * @param string $keyPath
     *
     * @return [string]
     */
    static function valueToNames($model, string $keyPath): array {
        return CBConvert::valueToNames(
            CBModel::value($model, $keyPath)
        );
    }



    /**
     * This is a convenience function for:
     *
     *      CBConvert::valueToString(CBModel::value(...));
     *
     * @param mixed $model
     * @param string $keyPath
     *
     * @return string
     */
    static function valueToString($model, string $keyPath): string {
        return CBConvert::valueToString(
            CBModel::value($model, $keyPath)
        );
    }

}

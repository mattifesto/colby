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
 * CBModel_toModel() interface which is called by the CBModel::toModel()
 * function. In the context of buiding models, we refer to the source model as a
 * spec. The spec is still a model, but the word spec is used in this context to
 * indicate that this model is the source model.
 *
 * As an example, classes implementing the CBView_render() interface use model
 * data to render HTML content. Those classes usually also use model data to
 * generate search text and build models from specs.
 */
final class CBModel {

    /**
     * This function performs a deep clone of a model by serializing it to JSON
     * and then unserializing it. Since models are always serialized to JSON
     * this method of cloning is will always produce a valid clone.
     *
     * @param object $model
     *
     *      This parameter does not technically have to be a model. That is, it
     *      does not need to have className property or a valid ID.
     *
     * @return object
     */
    static function clone(stdClass $model): stdClass {
        return json_decode(json_encode($model));
    }

    /**
     * This function can be used to generate IDs for specs, such as specs
     * imported from CSV files.
     *
     * @param object $spec
     *
     * @return hex160|null
     */
    static function toID(stdClass $spec): ?string {
        if (is_callable($function = "{$spec->className}::CBModel_toID")) {
            return call_user_func($function, $spec);
        } else {
            return null;
        }
    }

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
     *      should not occur during CBModel::toModel().
     *
     *      @NOTE
     *      If a model needs an ID, for example because it's going to be saved,
     *      that ID must be set on the spec. If a spec ID property is set it
     *      must be a valid hex160 and will always be copied to the model.
     *
     *      Generated IDs:
     *
     *      Sometimes specs are imported from CSV files created from
     *      spreadsheets edited by website administrators. Those users may
     *      specify human readable unique identifiers like product codes instead
     *      of IDs. The import process will notice that a spec doesn't have an
     *      ID and will call CBModel::toID() to generate an ID which it will
     *      then set on the spec.
     *
     *      Any ID generation must be done for a spec before it is used with
     *      this function.
     *
     *      @NOTE
     *      Since title is such a commonly used property, this function will
     *      perform a basic transfer of title from the spec to the model if the
     *      model produced doesn't have a title set.
     *
     * @param mixed $spec
     *
     *      For this function to successfully build a model, the spec's class
     *      must exist and have the CBModel_toModel() interface implemented.
     *
     *      The $spec parameter is not typed so that you can do somthing like:
     *
     *          $model = CBModel::build(CBModel::value($spec, 'image'));
     *
     *      If the value returned by CBModel::value() is not an object
     *      CBModel::build() will return null.
     *
     * @return ?model
     *
     *      This function will return null if the CBModel_toModel() interface is
     *      not implemented. That function may return null for specs that don't
     *      meet its basic requirements. The CBImage class, for example, has a
     *      number of specific spec requirements. However, most classes can
     *      build a model from a completely empty spec.
     */
    static function build($spec): ?stdClass {
        $className = CBModel::valueToString($spec, 'className');

        if (empty($className)) {
            return null;
        }

        $model = null;

        if (is_callable($function = "{$className}::CBModel_build")) {
            $model = call_user_func($function, $spec);
        } else if (is_callable($function = "{$className}::CBModel_toModel")) {
            $model = call_user_func($function, $spec);
        }

        if (!is_object($model)) {
            return null;
        }

        $model->className = $className;

        if (!empty($spec->ID)) {
            if (CBHex160::is($spec->ID)) {
                $model->ID = $spec->ID;
            } else {
                return null;
            }
        }

        if (!isset($model->title)) {
            $model->title = trim(CBModel::valueToString($spec, 'title'));
        }

        return $model;
    }

    /**
     * @deprecated use CBModel::build()
     */
    static function toModel(stdClass $spec) {
        return CBModel::build($spec);
    }

    /**
     * @return string|null
     */
    static function toSearchText(stdClass $model) {
        $className = CBModel::value($model, 'className', '');
        $ID = CBModel::value($model, 'ID', '');
        $text = '';

        if (is_callable($function = "{$className}::CBModel_toSearchText")) {
            $text = call_user_func($function, $model);
        } else if (is_callable($function = "{$className}::modelToSearchText")) { // deprecated
            $text = call_user_func($function, $model);
        }

        return implode(' ', array_filter([$text, $className, $ID]));
    }

    /**
     * @param model $spec
     *
     * @return model
     *
     *      The returned model will always be a different object than the object
     *      passed as the $spec argument. However the returned value may be
     *      equal to the $spec argument object. You can compare the $spec object
     *      to the returned value using == to determine if any changes were made
     *      during the upgrade.
     */
    static function upgrade(stdClass $spec): stdClass {
        $className = CBModel::valueToString($spec, 'className');
        $upgradedSpec = CBModel::clone($spec);

        if (is_callable($function = "{$className}::CBModel_upgrade")) {
            call_user_func($function, $upgradedSpec);

            $ID = CBModel::valueAsID($spec, 'ID');
            $upgradedID = CBModel::value($upgradedSpec, 'ID');

            if ($upgradedID != $ID) {
                $message = <<<EOT

                    When the {$className} model with the ID "{$ID}"
                    was upgraded using CBModel::upgrade(), the ID was
                    altered which is not allowed.

                    The upgrade was cancelled.

EOT;

                CBLog::log((object)[
                    'className' => __CLASS__,
                    'message' => $message,
                    'severity' => 3,
                ]);

                return CBModel::clone($spec);
            }
        }

        return $upgradedSpec;
    }

    /**
     * @param mixed $model
     *
     *  The $model parameter is generally expected to be a stdClass instance or
     *  `null`, but it can be any value such as `42`. If it is not stdClass this
     *  function will treat is as `null` and return the default value.
     *
     *  This behavior reduces the amount of validation code required in many
     *  cases. For instance, it allows code to fetch a model and not validate
     *  that the model exists (the model value may be `false` in this case)
     *  before checking to see if a value is set.
     *
     * @param string $keyPath
     *
     *      Examples: "height", "width", "image.height",
     *                "image.alternativeText.text"
     *
     * @param mixed? $default
     * @param function? $transform
     *
     * @return mixed
     */
    static function value($model, $keyPath, $default = null, callable $transform = null) {
        $keys = explode('.', $keyPath);
        $propertyName = array_pop($keys);

        foreach($keys as $key) {
            if (isset($model->{$key}) && is_object($model->{$key})) {
                $model = $model->{$key};
            } else {
                return $default;
            }
        }

        if (isset($model->{$propertyName})) {
            if ($transform !== null) {
                return call_user_func($transform, $model->{$propertyName});
            } else {
                return $model->{$propertyName};
            }
        } else {
            return $default;
        }
    }

    /**
     * @deprecated use:
     *
     *      CBModel::valueToArray();
     *
     * @NOTE
     *
     *      This function is misnamed because "valueAs" functions are supposed
     *      to be able to return null.
     *
     * This function is used to get the value of a model property that is
     * expected to be an array. Unset and non-array values will be returned as
     * and empty array.
     *
     * This function exists because during rendering this functionality is often
     * needed and is difficult to perform correctly.
     *
     * @param object? $model
     * @param string $keyPath
     * @param function? $transform
     *
     * @return [mixed]
     */
    static function valueAsArray($model, $keyPath, callable $transform = null) {
        $value = CBModel::value($model, $keyPath);

        if (!is_array($value)) {
            $value = [];
        }

        if ($transform !== null) {
            $value = call_user_func($transform, $value);
        }

        return $value;
    }

    /**
     * This is a convenience function for:
     *
     *      CBConvert::valueAsHex160(CBModel::value(...))
     *
     * @param mixed $model
     * @param string $keyPath
     *
     * @return hex160|null
     */
    static function valueAsID($model, string $keyPath): ?string {
        return CBConvert::valueAsHex160(CBModel::value($model, $keyPath));
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
        return CBConvert::valueAsInt(CBModel::value($model, $keyPath));
    }

    /**
     * This is a convenience function for:
     *
     *      CBConvert::valueAsModel(CBModel::value(...))
     *
     * @param mixed $model
     * @param string $keyPath
     *
     * @return ?model
     */
    static function valueAsModel($model, string $keyPath, array $classNames = []): ?stdClass {
        return CBConvert::valueAsModel(CBModel::value($model, $keyPath), $classNames);
    }

    /**
     * @deprecated use:
     *
     *      CBModel::value($model, $keyPath, [], 'CBConvert::stringToCSSClassNames');
     *
     * This function parses a string value into an array of names. This is used
     * for class names style properties.
     *
     * "one, , two    three" => ['one', 'two', 'three']
     *
     * @param object? $model
     * @param string $keyPath
     *
     * @return [string]
     */
    static function valueAsNames($model, $keyPath): array {
        return CBModel::value($model, $keyPath, [], 'CBConvert::stringToCSSClassNames');
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
        return CBConvert::valueAsObject(CBModel::value($model, $keyPath));
    }

    /**
     * @deprecated use:
     *
     *      CBModel::value($model, $keyPath, [], 'CBConvert::valueToArrayOfObjects');
     *
     * This function filters the array, but its replacement does not. If
     * filtering is a common or important feature add another new function to
     * CBConvert and document the reasons and uses.
     *
     * @param object? $model
     * @param string $keyPath
     *
     * @return [object]
     */
    static function valueAsObjects($model, $keyPath) {
        $array = CBModel::valueAsArray($model, $keyPath);

        return array_values(array_filter($array, function($item) {
            return is_object($item);
        }));
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
        return CBConvert::valueToArray(CBModel::value($model, $keyPath));
    }

    /**
     * Use this function when a property may hold a spec which you would like
     * converted to a model.
     *
     * @NOTE
     *
     *      This function is misnamed. See CBConvert for the rules on "valueAs"
     *      and "valueTo" functions.
     *
     *      This function is too complex to be understood as quickly as it
     *      should be. The spec fixing functionality should be broken out into
     *      another function.
     *
     *      Issue: fixing a model that does't have a class name is a bad idea.
     *      Rules exist so that this type of thing is not needed. I think this
     *      is related to CBImage specs somehow. Find those cases and make a
     *      fixing solution for them.
     *
     *
     * @param object? $model
     * @param string $keyPath
     *
     *      The requested property value must be an object or this function will
     *      return null.
     *
     * @param string? $expectedClassName
     *
     *      Scenarios:
     *
     *      spec->className is not empty, expectedClassName is empty
     *
     *          spec is converted to a model
     *
     *      spec->className is not empty, expectedClassName is not empty
     *
     *          if spec->className != $expectedClassName null is returned
     *          otherwise the spec is converted to a model
     *
     *      spec->ClassName is empty, expectedClassName is empty
     *
     *          null is returned
     *
     *      spec->className is empty, expectedClassName is not empty
     *
     *          spec->className is set to $expectedClassName
     *          spec is converted to a model
     *
     *      @NOTE 2017.08.10 A spec should always have its className property
     *            set, but in the past that hasn't always been the case. Using
     *            the expectedClassName as the className for specs that need it
     *            solves some backward compatability issues.
     *
     * @return object|null
     *
     *      This function returns null if the property does not contain an
     *      object or if the object is not a spec.
     */
    static function valueToModel($model, $keyPath, $expectedClassName = null) {
        $spec = CBModel::value($model, $keyPath);

        if (!is_object($spec)) {
            return null;
        }

        if (empty($spec->className)) {
            if (empty($expectedClassName)) {
                return null;
            }

            $spec->className = $expectedClassName;
        } else if (!empty($expectedClassName) && $spec->className != $expectedClassName) {
            return null;
        }

        return CBModel::toModel($spec);
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
        return CBConvert::valueToObject(CBModel::value($model, $keyPath));
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
        return CBConvert::valueToString(CBModel::value($model, $keyPath));
    }
}

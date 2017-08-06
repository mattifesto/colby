<?php

final class CBModel {

    /**
     * @param stdClass $args->spec
     *
     * @return null
     */
    static function importSpecForTask(stdClass $args) {
        CBDB::transaction(function () use ($args) {
            CBModels::save([$args->spec], /* force: */ true);
        });

        CBLog::addMessage('CBModel', 6, "A task was run to import a spec of class '{$model->className}'");
    }

    /**
     * This is the official way to convert a spec to a model.
     *
     * Reading this function will help developers understand the exact
     * interactions and requirements of specs and models.
     *
     * @NOTE It is technically possible for the model to receive a generated ID
     *       that is different from the spec's ID. In this case, the spec is
     *       probably "wrong" to have an ID set, but it's not an error for the
     *       same reason that it's never an error for a spec to have properties
     *       set that aren't used by the model. The model's ID is the ID used
     *       when saving the model.
     *
     * @NOTE If the model generates an ID, it must be deterministic so that
     *       the same ID will be produced from another call to specToModel().
     *       Good candidates for this are specs with unique string properties
     *       such as `productCode`.
     *
     * @NOTE A model is allowed to have a different className than the spec.
     *       This can be used by deprecated classes to generate non-deprecated
     *       models.
     *
     * @param object $spec
     *
     * @return object|null
     *
     *      A spec is not an object that is guaranteed to be able to produce a
     *      model. One of the few requirements of a spec is that have a set and
     *      supported className property. Othewise the spec is just an object
     *      and null will be returned as the model.
     *
     *      To be active, a model's className must exist in the system and have
     *      functions implemented related to its desired actions.
     */
    static function specToModel(stdClass $spec) {
        if (empty($spec->className) || !is_callable($function = "{$spec->className}::specToModel")) {
            return null;
        }

        $model = call_user_func($function, $spec);

        if (empty($model->className)) {
            return null;
        }

        if (empty($model->ID) && !empty($spec->ID) && CBHex160::is($spec->ID)) {
            $model->ID = $spec->ID;
        }

        return $model;
    }

    /**
     * This is an alternate way to convert a spec into a model when the spec
     * values are not reliable. If the spec cannot be propertly converted to a
     * model, null is returned.
     *
     * @param object? $spec
     * @param string? $expectedClassName
     *
     *      If you are expecting a specific class name, pass that class name as
     *      this parameter. If the spec has its className property set and it is
     *      different than this class name, the function will return null.
     *
     *      If the spec does not have its className property set, this parameter
     *      will be used as the class name. This is to provide backward
     *      compatability with the use of specs that incorrectly didn't specify
     *      class names.
     *
     * @return object|null
     */
    static function specToOptionalModel($spec = null, $expectedClassName = null) {
        if (!is_object($spec)) {
            return null;
        }

        if (empty($spec->className)) {
            $spec = clone $spec;
            $spec->className = $expectedClassName;
        } else if (!empty($expectedClassName) && ($spec->className !== $expectedClassName)) {
            return null;
        }

        return CBModel::specToModel($spec);
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

        $result = implode(' ', array_filter([$text, $className, $ID]));

        error_log($result);

        return $result;
    }

    /**
     * @param stdClass? $model
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
    static function valueAsNames($model, $keyPath) {
        $value = CBModel::value($model, $keyPath, '');

        if (!is_string($value)) {
            return [];
        }

        $names = preg_split('/[\s,]+/', $value, null, PREG_SPLIT_NO_EMPTY);

        if ($names === false) {
            throw new RuntimeException("preg_split() returned false");
        }

        return $names;
    }

    /**
     * This function is used when you expect a model property to contain an
     * object. If the property does contain an object, it will be returned. If
     * not, an empty object will be returned. A non-object property value is
     * ignored.
     *
     * @param object? $model
     * @param string $keyPath
     *
     * @return object
     */
    static function valueAsObject($model, $keyPath) {
        $value = CBModel::value($model, $keyPath);

        if (is_object($value)) {
            return $value;
        } else {
            return (object)[];
        }
    }

    /**
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
     * @deprecated use CBModel::valueToModel()
     *
     * This function uses the $expectedClassName parameter in a deprecated way.
     * If the spec doesn't have a className set it will use $expectedClassName
     * as its className. If you have a scenario where you need this type of
     * functionality, this a scenario that needs a bug fix.
     *
     * The known case of this is some old CBImage specs that didn't have a class
     * name which has been fixed for the future, but not for some past specs.
     * Those cases should be handled locally.
     *
     * @return object|null
     */
    static function valueAsSpecToModel($model, $keyPath, $expectedClassName = null) {
        $value = CBModel::value($model, $keyPath);

        return CBModel::specToOptionalModel($value, $expectedClassName);
    }

    /**
     * Use this function when a property may hold a spec which you would like
     * converted to a model.
     *
     * @NOTE The name "valueTo" instead of "valueAs" indicates that the original
     *       property value may be undergoing conversion.
     *
     * @param object? $model
     * @param string $keyPath
     * @param string? $expectedClassName
     *
     *      If the requested property holds an object and that object does not
     *      have its className property set to $expectedClassName this function
     *      will return null.
     *
     * @return object|null
     *
     *      This function returns null if the property does not contain an
     *      object or if the object is not a spec.
     */
    static function valueToModel($model, $keyPath, $expectedClassName = null) {
        $spec = CBModel::value($model, $keyPath);

        if (is_object($spec)) {
            if (!empty($expectedClassName)) {
                if (empty($spec->className) || $spec->className !== $expectedClassName) {
                    return null;
                }
            }

            return CBModel::specToModel($spec);
        } else {
            return null;
        }
    }

    /**
     * @NOTE The name "valueTo" instead of "valueAs" indicates that the original
     *       property value may be undergoing conversion.
     *
     * @return [object]
     */
    static function valueToModels($model, $keyPath) {
        $models = [];
        $specs = CBModel::valueAsArray($model, $keyPath);

        foreach ($specs as $spec) {
            if (is_object($spec) && ($model = CBModel::specToModel($spec))) {
                $models[] = $model;
            }
        }

        return $models;
    }
}

<?php

final class CBModel {

    /**
     * @param stdClass $args->spec
     *
     * @return null
     */
    public static function importSpecForTask(stdClass $args) {
        $spec = $args->spec;
        $model = CBModel::specToOptionalModel($spec);

        if ($model !== null) {
            try {
                Colby::query('START TRANSACTION');
                CBModels::saveTuples([(object)[
                    'spec' => $spec,
                    'model' => $model,
                    'version' => 'force',
                ]]);
                Colby::query('COMMIT');
            } catch (Exception $exception) {
                Colby::query('ROLLBACK');
                throw $exception;
            }

            CBLog::addMessage('CBModel', 6, "A task was run to import a spec of class '{$model->className}'");
        }
    }

    /**
     * @return string|null
     */
    public static function modelToOptionalSearchText(stdClass $model) {
        if (isset($model->className) && is_callable($function = "{$model->className}::modelToOptionalSearchText")) {
            return call_user_func($function, $model);
        } else {
            return null;
        }
    }

    /**
     * This function looks for a spec array on a named property of a spec and
     * if one exists converts the specs into models removing the elements where
     * the spec element cannot be converted.
     *
     * @param stdClass $spec
     * @param string $propertyName
     *
     * @return [stdClass]
     */
    public static function namedSpecArrayToModelArray(stdClass $spec, $propertyName) {
        if (isset($spec->{$propertyName}) && is_array($specArray = $spec->{$propertyName})) {
            $modelArray = array_map('CBModel::specToOptionalModel', $specArray);
            return array_filter($modelArray, function ($model) { return $model !== null; });
        } else {
            return [];
        }
    }

    /**
     * This is the official way to convert a spec into a model. If no function
     * is available to convert the spec into a model this function will return
     * null.
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
            if (empty($expectedClassName)) {
                return null;
            } else {
                $className = $expectedClassName;
            }
        } else {
            if (!empty($expectedClassName) && $spec->className !== $expectedClassName) {
                return null;
            }

            $className = $spec->className;
        }

        if (is_callable($function = "{$className}::specToModel")) {
            return call_user_func($function, $spec);
        } else {
            return null;
        }
    }

    /**
     * @param stdClass? $model
     *  The $model parameter is generally expected to be a stdClass instance or
     *  `null`, but it can be any value such as `42`. If it is not stdClass this
     *  function will treat is as `null` and return the default value.
     *
     *  This behavior reduces the amount of validation code required in many
     *  cases. For instance, it allows code to fetch a model and not validate
     *  that the model exists (the model value may be `false` in this case)
     *  before checking to see if a value is set.
     * @param string $keyPath
     *      Examples: "height", "width", "image.height", "image.alternativeText.text"
     * @param mixed? $default
     * @param function? $transform
     *
     * @return mixed
     */
    static function value($model = null, $keyPath, $default = null, callable $transform = null) {
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
     * @param stdClass? $model
     * @param string $keyPath
     * @param function? $transform
     *
     * @return [mixed]
     */
    static function valueAsArray($model = null, $keyPath, callable $transform = null) {
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
    static function valueAsNames($model = null, $keyPath) {
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
    static function valueAsObject($model = null, $keyPath) {
        $value = CBModel::value($model, $keyPath);

        if (is_object($value)) {
            return $value;
        } else {
            return (object)[];
        }
    }

    /**
     * This function is used when you expect a model property to contain an
     * optional spec which, if set, you would like converted to a model.
     *
     * @param object? $model
     * @param string $keyPath
     * @param string? $expectedClassName
     *
     *      Use this parameter if you expect the spec property value to have a
     *      specific class name and would rather have null returned if it does
     *      not have that class name.
     *
     * @return object|null
     */
    static function valueAsSpecToModel($model = null, $keyPath, $expectedClassName = null) {
        $value = CBModel::value($model, $keyPath);

        return CBModel::specToOptionalModel($value, $expectedClassName);
    }
}

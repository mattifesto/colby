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
     * @param stdClass $spec
     *
     * @return stdClass|null
     */
    public static function specToOptionalModel(stdClass $spec) {
        if (isset($spec->className) && is_callable($function = "{$spec->className}::specToModel")) {
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
}

<?php

final class CBModel {

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
     * @param string $propertyName
     * @param mixed? $default
     * @param function? $transform
     *
     * @return mixed
     */
    public static function value($model = null, $propertyName, $default = null, callable $transform = null) {
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
}

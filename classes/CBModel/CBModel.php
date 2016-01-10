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
     * @param stdClass $model
     * @param string $propertyName
     * @param mixed $default
     * @param function $transform
     *
     * @return mixed
     */
    public static function value(stdClass $model, $propertyName, $default = null, callable $transform = null) {
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

<?php

final class CBModel {

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

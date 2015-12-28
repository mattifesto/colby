<?php

final class CBModel {

    /**
     * @param stdClass $model
     * @param string $propertyName
     * @param mixed $default
     *
     * @return mixed
     */
    public static function value(stdClass $model, $propertyName, $default = null) {
        if (isset($model->{$propertyName})) {
            return $model->{$propertyName};
        } else {
            return $default;
        }
    }
}

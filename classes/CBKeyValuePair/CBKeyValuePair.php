<?php

final class CBKeyValuePair {

    /**
     * @param model $spec
     *
     * @return ?model
     */
    static function CBModel_build(stdClass $spec): ?stdClass {
        $key = CBKeyValuePair::valueAsKey($spec, 'key');

        if ($key === null) {
            return null;
        }

        return (object)[
            'key' => $key,
            'value' => json_decode(CBModel::valueToString($spec, 'valueAsJSON')),
        ];
    }

    /**
     * @param mixed $spec
     * @param string $keyPath
     *
     * @return ?string
     */
    private static function valueAsKey($spec, string $keyPath): ?string {
        $key = trim(CBModel::valueToString($spec, $keyPath));

        if (preg_match('/^[a-zA-Z][a-zA-Z0-9]*$/', $key)) {
            return $key;
        } else {
            return null;
        }
    }

    /**
     * This function looks for an array of CBKeyValuePair specs and turns the
     * keys and values contained in those specs into property values on an
     * object.
     *
     * @param mixed $spec
     * @param string $keyPath
     *
     * @return object
     */
    static function valueToObject($spec, string $keyPath): stdClass {
        $items = CBConvert::valueToArray(CBModel::value($spec, $keyPath));

        $models = array_map(function ($item) {
            $spec = CBConvert::valueAsModel($item, ['CBKeyValuePair']);
            return CBModel::build($spec);
        }, $items);

        $object = (object)[];

        foreach ($models as $model) {
            if ($model) {
                $object->{$model->key} = $model->value;
            }
        }

        return $object;
    }
}

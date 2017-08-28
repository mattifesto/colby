<?php

final class CBKeyValuePair {

    /**
     * @param mixed $value
     *
     * @return string|null
     */
    static function keyval($value) {
        $key = trim(strval($value));

        if (preg_match('/^[a-zA-Z][a-zA-Z0-9]*$/', $key)) {
            return $key;
        } else {
            return null;
        }
    }

    /**
     * @param stdClass $spec
     *
     * @return stdClass
     */
    static function specToModel(stdClass $spec) {
        return (object)[
            'className' => __CLASS__,
            'key' => CBModel::value($spec, 'key', null, 'CBKeyValuePair::keyval'),
            'value' => json_decode(CBModel::value($spec, 'valueAsJSON')),
        ];
    }
}

<?php

final class CBKeyValuePair {

    /* -- CBModel interfaces -- -- -- -- -- */

    /**
     * @param object $spec
     *
     * @return object
     */
    static function CBModel_build(stdClass $spec): stdClass {
        $key = CBModel::valueAsName($spec, 'key') ?? '';

        $value = json_decode(
            CBModel::valueToString(
                $spec,
                'valueAsJSON'
            )
        );

        return (object)[
            'key' => $key,
            'value' => $value,
        ];
    }
    /* CBModel_build() */


    /* -- functions -- -- -- -- -- */

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
        $keyValuePairSpecs = CBModel::valueToArray($spec, $keyPath);

        $keyValuePairSpecs =
        array_values(
            array_filter(
                array_map(
                    function ($currentSpec) {
                        return CBConvert::valueAsModel(
                            $currentSpec,
                            ['CBKeyValuePair']
                        );
                    },
                    $keyValuePairSpecs
                )
            )
        );

        $keyValuePairModels = array_map(
            function ($currentSpec) {
                return CBModel::build($currentSpec);
            },
            $keyValuePairSpecs
        );

        $object = (object)[];

        foreach ($keyValuePairModels as $keyValuePairModel) {
            if (!empty($keyValuePairModel->key)) {
                $object->{$keyValuePairModel->key} = $keyValuePairModel->value;
            }
        }

        return $object;
    }
    /* valueToObject() */
}

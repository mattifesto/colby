<?php

/**
 * This view is used to make tests simple. It has very simple behavior that
 * makes it easy to use when writing test.
 */
final class CBTestView {

    /**
     * @param model $spec
     *
     * @return ?model
     */
    static function CBModel_build(stdClass $spec): ?stdClass {
        return (object)[
            'value' => CBModel::valueAsInt($spec, 'value'),
        ];
    }

    /**
     * @param model $model
     *
     * @return string
     */
    static function CBModel_toSearchText(stdClass $model): string {
        return CBModel::valueToString($model, 'value');
    }
}

<?php

/**
 * A CBNote is used to hold a note written by a user at a specific time. Note
 * models can be used to hold comments or notes. Multiple note models may be
 * stored in an array that is a property of another model.
 */
final class CBNote {

    /**
     * @param object $spec
     *
     * @return object
     */
    static function CBModel_build(stdClass $spec): stdClass {
        $text = CBModel::valueToString($spec, 'text');

        if (trim($text) === '') {
            throw CBException::createModelIssueException(
                'The "text" property must have content.',
                $spec,
                'f10b522f45f4dced8c95639c3cd1ce0a6316f8af'
            );
        }

        $timestamp = CBModel::valueAsInt($spec, 'timestamp');

        if ($timestamp === null) {
            throw CBException::createModelIssueException(
                'The "timestamp" property must be an integer.',
                $spec,
                '52ba6b7aaefba2b505610faa5b319fbf2288bed0'
            );
        }

        $model = (object)[
            'text' => CBModel::valueToString($spec, 'text'),
            'timestamp' => $timestamp,
            'userID' => CBModel::valueAsID($spec, 'userID'),
        ];

        return $model;
    }
    /* CBModel_build() */
}

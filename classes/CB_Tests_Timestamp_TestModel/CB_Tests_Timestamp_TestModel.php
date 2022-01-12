<?php

final class
CB_Tests_Timestamp_TestModel {

    /* -- CBModel interfaces -- */



    /**
     * @param object $spec
     *
     * @return object
     */
    static function
    CBModel_build(
        stdClass $spec
    ): stdClass {
        $model = (object)[];

        CB_Tests_Timestamp_TestModel::setCBTimestamp(
            $model,
            CBModel::build(
                CB_Tests_Timestamp_TestModel::getCBTimestamp(
                    $spec
                )
            )
        );

        return $model;
    }
    /* CBModel_build() */



    /**
     * @param object $model
     *
     * @return [<CB_Timestamp>]
     */
    static function
    CBModel_getCBTimestamps(
        stdClass $model
    ): array {
        return [
            CB_Tests_Timestamp_TestModel::getCBTimestamp(
                $model
            )
        ];
    }
    /* CBModel_getCBTimestamps() */



    /* -- accessors -- */



    /**
     * @param object $model
     *
     * @return object|null
     */
    static function
    getCBTimestamp(
        stdClass $model
    ): ?stdClass {
        return CBModel::valueAsModel(
            $model,
            'CB_Tests_Timestamp_TestModel_cbtimestamp_property',
            'CB_Timestamp'
        );
    }
    /* getCBTimestamp() */



    /**
     * @param object $model
     * @param object $cbtimestampModel
     *
     * @return void
     */
    static function
    setCBTimestamp(
        stdClass $model,
        stdClass $cbtimestampModel
    ): void {
        $verifiedCBTimestampModel = CBConvert::valueAsModel(
            $cbtimestampModel,
            'CB_Timestamp'
        );

        if (
            $verifiedCBTimestampModel === null
        ) {
            throw new CBExceptionWithValue(
                'The cbtimestampModel argument is not a CB_Timestamp model.',
                $cbtimestampModel,
                '317c12a0bd53dbff1709bdeac13f1d7fb385d8d3'
            );
        }

        $model->CB_Tests_Timestamp_TestModel_cbtimestamp_property = (
            $cbtimestampModel
        );
    }
    /* setCBTimestamp() */

}

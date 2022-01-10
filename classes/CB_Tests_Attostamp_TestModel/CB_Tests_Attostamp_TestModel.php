<?php

final class
CB_Tests_Attostamp_TestModel {

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

        CB_Tests_Attostamp_TestModel::setAttostamp(
            $model,
            CBModel::build(
                CB_Tests_Attostamp_TestModel::getAttostamp(
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
     * @return [<CB_Attostamp>]
     */
    static function
    CBModel_getAttostampModels(
        stdClass $model
    ): array {
        return [
            CB_Tests_Attostamp_TestModel::getAttostamp(
                $model
            )
        ];
    }
    /* CBModel_getAttostamps() */



    /* -- accessors -- */



    /**
     * @param object $model
     *
     * @return object|null
     */
    static function
    getAttostamp(
        stdClass $model
    ): ?stdClass {
        return CBModel::valueAsModel(
            $model,
            'CB_Tests_Attostamp_TestModel_attostamp_property',
            'CB_Attostamp'
        );
    }
    /* getAttostamp() */



    /**
     * @param object $model
     * @param object $attostampModel
     *
     * @return void
     */
    static function
    setAttostamp(
        stdClass $model,
        stdClass $attostampModel
    ): void {
        $verifiedAttostampModel = CBConvert::valueAsModel(
            $attostampModel,
            'CB_Attostamp'
        );

        if (
            $verifiedAttostampModel === null
        ) {
            throw new CBExceptionWithValue(
                'The attostampModel argument is not a CB_Attostamp model.',
                $attostampModel,
                '317c12a0bd53dbff1709bdeac13f1d7fb385d8d3'
            );
        }

        $model->CB_Tests_Attostamp_TestModel_attostamp_property = (
            $attostampModel
        );
    }
    /* setAttostamp() */

}

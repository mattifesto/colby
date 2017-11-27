<?php

final class CBModelTests {

    /**
     * This test runs a CBModel::toModel test for all known classes.
     */
    static function toModelTest() {
        $classNames = CBAdmin::fetchClassNames();

        foreach ($classNames as $className) {
            $spec = (object)[
                'className' => $className,
            ];

            $model = CBModel::toModel($spec);
        }
    }

    /**
     * This test checks the result to CBModel::toModel() when the converstion
     * function does the minimum amount of work.
     */
    static function toModelMinimalImplementationTest() {
        $spec = (object)[
            'className' => 'CBModelTests_TestClass1',
            'ID' => 'c4247a40d9d85524607e6e87cc1d138806765d59',
            'title' => 'Test Title',
        ];

        $model = CBModel::toModel($spec);
        $expectedModel = (object)[
            'className' => 'CBModelTests_TestClass1',
            'ID' => 'c4247a40d9d85524607e6e87cc1d138806765d59',
            'title' => 'Test Title',
        ];

        if ($model != $expectedModel) {
            throw new Exception('The model differs from the expected model.');
        }
    }
}


final class CBModelTests_TestClass1 {

    /**
     * @param object $spec
     *
     * @return object
     */
    static function CBModel_toModel(stdClass $spec) {
        return (object)[];
    }
}

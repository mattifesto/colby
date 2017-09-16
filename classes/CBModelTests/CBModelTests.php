<?php

final class CBModelTests {

    /**
     * This test runs a CBModel::toModel test for all known classes.
     */
    static function toModelTest() {
        $classNames = CBModelTests::getClassNames();

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

    /**
     * @return [string]
     */
    static function getClassNames() {
        $classNames = [];

        foreach (Colby::$libraryDirectories as $libraryDirectory) {
            $libraryClassesDirectory = $libraryDirectory . '/classes';
            $libraryClassDirectories = glob("{$libraryClassesDirectory}/*" , GLOB_ONLYDIR);
            $libraryClassNames = array_map('basename', $libraryClassDirectories);

            $classNames = array_merge($classNames, $libraryClassNames);
        }

        return array_values(array_unique($classNames));
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

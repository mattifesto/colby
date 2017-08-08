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

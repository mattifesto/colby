<?php

final class
CBModelTemplateCatalogTests {

    /* -- CBTest interfaces -- -- -- -- -- */



    /**
     * @return [object]
     */
    static function
    CBTest_getTests(
    ): array {
        return [
            (object)[
                'name' => 'test',
                'type' => 'server',
            ],
        ];
    }
    /* CBTest_getTests() */



    /* -- tests -- -- -- -- -- */



    /**
     * @return object
     */
    static function
    test(
    ): stdClass {
        CBModelTemplateCatalog::$testID = (
            '2933b216beb27fe98400106b4d235165b0e12852'
        );

        CBModelTemplateCatalog::CBInstall_install();

        CBModelTemplateCatalog::install(
            'CBModelTemplateCatalogTests_template1'
        );

        CBModelTemplateCatalog::install(
            'CBModelTemplateCatalogTests_template2'
        );

        CBModelTemplateCatalog::install(
            'CBModelTemplateCatalogTests_template3'
        );

        CBModelTemplateCatalog::install(
            'CBModelTemplateCatalogTests_template3'
        );

        $templateClassNames = (
            CBModelTemplateCatalog::fetchTemplateClassNamesByTargetClassName(
                'Foo'
            )
        );

        $expectedClassNames = [
            'CBModelTemplateCatalogTests_template1',
            'CBModelTemplateCatalogTests_template2',
        ];

        if (
            $templateClassNames != $expectedClassNames
        ) {
            return CBTest::resultMismatchFailure(
                'test 1',
                $templateClassNames,
                $expectedClassNames
            );
        }

        $templateClassNames = (
            CBModelTemplateCatalog::fetchTemplateClassNamesByTargetClassName(
                'Bar'
            )
        );

        $expectedClassNames = [
            'CBModelTemplateCatalogTests_template3',
        ];

        if (
            $templateClassNames != $expectedClassNames
        ) {
            return CBTest::resultMismatchFailure(
                'test 2',
                $templateClassNames,
                $expectedClassNames
            );
        }

        $templateClassNames = (
            CBModelTemplateCatalog::fetchTemplateClassNamesByTargetClassName(
                'Baz'
            )
        );

        $expectedClassNames = [];

        if (
            $templateClassNames != $expectedClassNames
        ) {
            return CBTest::resultMismatchFailure(
                'test 3',
                $templateClassNames,
                $expectedClassNames
            );
        }

        CBDB::transaction(
            function () {
                CBModels::deleteByID(
                    CBModelTemplateCatalog::$testID
                );
            }
        );

        CBModelTemplateCatalog::$testID = null;

        return (object)[
            'succeeded' => true,
        ];
    }
    /* CBTest_test() */

}



final class CBModelTemplateCatalogTests_template1 {
    static function CBModelTemplate_spec(): stdClass {
        return (object)[
            'className' => 'Foo',
        ];
    }
}



final class CBModelTemplateCatalogTests_template2 {
    static function CBModelTemplate_spec(): stdClass {
        return (object)[
            'className' => 'Foo',
        ];
    }
}



final class CBModelTemplateCatalogTests_template3 {
    static function CBModelTemplate_spec(): stdClass {
        return (object)[
            'className' => 'Bar',
        ];
    }
}

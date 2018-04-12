<?php

final class CBModelTemplateCatalogTests {

    /**
     * @return object
     */
    static function CBTest_test(): stdClass {
        CBModelTemplateCatalog::$testID = '2933b216beb27fe98400106b4d235165b0e12852';

        CBModelTemplateCatalog::CBInstall_install();
        CBModelTemplateCatalog::install('CBModelTemplateCatalogTests_template1');
        CBModelTemplateCatalog::install('CBModelTemplateCatalogTests_template2');
        CBModelTemplateCatalog::install('CBModelTemplateCatalogTests_template3');
        CBModelTemplateCatalog::install('CBModelTemplateCatalogTests_template3');

        $templateClassNames = CBModelTemplateCatalog::fetchTemplateClassNamesByTargetClassName('Foo');
        $expectedClassNames = [
            'CBModelTemplateCatalogTests_template1',
            'CBModelTemplateCatalogTests_template2',
        ];

        if ($templateClassNames != $expectedClassNames) {
            return (object)[
                'message' =>
                    "1: The template class names do not match the expected class names.\n\n" .
                    CBConvertTests::resultAndExpectedToMessage($templateClassNames, $expectedClassNames),
            ];
        }

        $templateClassNames = CBModelTemplateCatalog::fetchTemplateClassNamesByTargetClassName('Bar');
        $expectedClassNames = [
            'CBModelTemplateCatalogTests_template3',
        ];

        if ($templateClassNames != $expectedClassNames) {
            return (object)[
                'message' =>
                    "2: The template class names do not match the expected class names.\n\n" .
                    CBConvertTests::resultAndExpectedToMessage($templateClassNames, $expectedClassNames),
            ];
        }

        $templateClassNames = CBModelTemplateCatalog::fetchTemplateClassNamesByTargetClassName('Baz');
        $expectedClassNames = [];

        if ($templateClassNames != $expectedClassNames) {
            return (object)[
                'message' =>
                    "3: The template class names do not match the expected class names.\n\n" .
                    CBConvertTests::resultAndExpectedToMessage($templateClassNames, $expectedClassNames),
            ];
        }

        CBModels::deleteByID(CBModelTemplateCatalog::$testID);

        CBModelTemplateCatalog::$testID = null;

        return (object)[
            'succeeded' => true,
        ];
    }

    /**
     * @return [[<class>, <name>]]
     */
    static function CBUnitTests_tests(): array {
        return [
            ['CBModelTemplateCatalog', 'test']
        ];
    }
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

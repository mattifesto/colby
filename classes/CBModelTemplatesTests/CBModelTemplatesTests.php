<?php

final class CBModelTemplatesTests {

    /**
     * This variable will be set to a substitute ID to by used by
     * CBModelTemplates while tests are running.
     */
    static $testID = null;

    /**
     * @return object
     */
    static function CBTest_test(): stdClass {
        CBModelTemplatesTests::$testID = '2933b216beb27fe98400106b4d235165b0e12852';

        CBModelTemplates::CBInstall_install();
        CBModelTemplates::installTemplate('CBModelTemplatesTests_template1');
        CBModelTemplates::installTemplate('CBModelTemplatesTests_template2');
        CBModelTemplates::installTemplate('CBModelTemplatesTests_template3');
        CBModelTemplates::installTemplate('CBModelTemplatesTests_template3');

        $templateClassNames = CBModelTemplates::fetchTemplateClassNames('Foo');
        $expectedClassNames = [
            'CBModelTemplatesTests_template1',
            'CBModelTemplatesTests_template2',
        ];

        if ($templateClassNames != $expectedClassNames) {
            return (object)[
                'message' =>
                    "1: The template class names do no match the expected class names.\n\n" .
                    CBConvertTests::resultAndExpectedToMessage($templateClassNames, $expectedClassNames),
            ];
        }

        $templateClassNames = CBModelTemplates::fetchTemplateClassNames('Bar');
        $expectedClassNames = [
            'CBModelTemplatesTests_template3',
        ];

        if ($templateClassNames != $expectedClassNames) {
            return (object)[
                'message' =>
                    "2: The template class names do no match the expected class names.\n\n" .
                    CBConvertTests::resultAndExpectedToMessage($templateClassNames, $expectedClassNames),
            ];
        }

        $templateClassNames = CBModelTemplates::fetchTemplateClassNames('Baz');
        $expectedClassNames = [];

        if ($templateClassNames != $expectedClassNames) {
            return (object)[
                'message' =>
                    "3: The template class names do no match the expected class names.\n\n" .
                    CBConvertTests::resultAndExpectedToMessage($templateClassNames, $expectedClassNames),
            ];
        }

        CBModels::deleteByID(CBModelTemplatesTests::$testID);

        CBModelTemplatesTests::$testID = null;

        return (object)[
            'succeeded' => true,
        ];
    }

    /**
     * @return [[<class>, <name>]]
     */
    static function CBUnitTests_tests(): array {
        return [
            ['CBModelTemplates', 'test']
        ];
    }
}

final class CBModelTemplatesTests_template1 {
    static function CBModelTemplate_spec(): stdClass {
        return (object)[
            'className' => 'Foo',
        ];
    }
}

final class CBModelTemplatesTests_template2 {
    static function CBModelTemplate_spec(): stdClass {
        return (object)[
            'className' => 'Foo',
        ];
    }
}

final class CBModelTemplatesTests_template3 {
    static function CBModelTemplate_spec(): stdClass {
        return (object)[
            'className' => 'Bar',
        ];
    }
}

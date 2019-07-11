<?php

final class CBSitePreferencesTests {

    /* -- CBTest interfaces -- -- -- -- -- */

    /**
     * @return [object]
     */
    static function CBTest_getTests(): array {
        return [
            (object)[
                'type' => 'server',
                'title' => 'CBSitePreferences',
                'name' => 'general',
            ],
        ];
    }
    /* CBTest_getTests() */


    /* -- tests -- -- -- -- -- */

    /**
     * @return object
     */
    static function CBTest_general(): stdClass {

        /* administratorEmails */

        $model = CBModel::build(
            (object)[
                'className' => 'CBSitePreferences',
                'administratorEmails' => (
                    '   matt@mattifesto.com ' .
                    ' matt@mattifesto2.com  ,' .
                    ' matt@mattifesto.com ' .
                    ' , , matt@mattifesto3.com ,'
                ),
            ]
        );

        $actualResult = $model->administratorEmails;

        $expectedResult = [
            'matt@mattifesto.com',
            'matt@mattifesto2.com',
            'matt@mattifesto3.com',
        ];

        if ($actualResult != $expectedResult) {
            return CBTest::resultMismatchFailure(
                'administratorEmails',
                $actualResult,
                $expectedResult
            );
        }

        /* CBSitePreferences::debug() */

        $value = CBSitePreferences::debug();

        if (!is_bool($value)) {
            throw new Exception(
                'CBSitePreferences::debug() should return a boolean value.'
            );
        }

        /* done */

        return (object)[
            'succeeded' => true,
        ];
    }
    /* CBTest_general() */
}

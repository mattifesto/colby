<?php

final class CBUserGroup_Tests {

    /* -- CBTest interfaces -- -- -- -- -- */



    /**
     * @return [object]
     */
    static function CBTest_getTests(): array {
        return [
            (object)[
                'name' => 'deprecatedGroupNameToUserGroupClassName',
                'type' => 'server',
            ],
        ];
    }
    /* CBTest_getTests() */



    /* -- tests -- -- -- -- -- */



    /**
     * @return object
     */
    static function CBTest_deprecatedGroupNameToUserGroupClassName(): stdClass {
        $tests = [
            [
                'Administrators',
                'CBAdministratorsUserGroup',
            ],
            [
                'Developers',
                'CBDevelopersUserGroup',
            ],
            [
                'Public',
                'CBPublicUserGroup',
            ],
            [
                '',
                null,
            ],
            [
                'random',
                null,
            ],
        ];

        for ($index = 0; $index < count($tests); $index += 1) {
            $test = $tests[$index];
            $testName = $test[0];
            $deprecatedGroupName = $test[0];
            $expectedUserGroupClassName = $test[1];

            $actualUserGroupClassName = (
                CBUserGroup::deprecatedGroupNameToUserGroupClassName(
                    $deprecatedGroupName
                )
            );

            if ($actualUserGroupClassName !== $expectedUserGroupClassName) {
                return CBTest::resultMismatchFailure(
                    $testName,
                    $actualUserGroupClassName,
                    $expectedUserGroupClassName
                );
            }
        }

        return (object)[
            'succeeded' => true,
        ];
    }
    /* CBTest_deprecatedGroupNameToUserGroupClassName() */

}

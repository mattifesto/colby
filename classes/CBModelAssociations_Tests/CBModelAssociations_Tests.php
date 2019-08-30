<?php

final class CBModelAssociations_Tests {

    /* -- CBTest interfaces -- -- -- -- -- */

    static function CBTest_getTests(): array {
        return [
            (object)[
                'name' => 'general',
                'title' => 'CBModelAssociations',
                'type' => 'server',
            ],
        ];
    }
    /* CBTest_getTests() */


    /* -- tests -- -- -- -- -- */

    /**
     * @object
     */
    static function CBTest_general(): stdClass {
        $ID1 = '67bafc3efdf61dd79f3bf95db8cb9a4f35389e56';
        $ID2 = '83e9db09a43a6449825389515a81ed45bf1f862a';

        $associationKey1 = 'CBModelAssociations_Tests_Key1';
        $associationKey2 = 'CBModelAssociations_Tests_Key2';

        $associatedID1 = '634f918586535eb230177dd1e5686e7402cfc791';
        $associatedID2_1 = '0aced83292225e29eb33a2e70d3ab5fc833b85df';
        $associatedID2_2 = '1082b2cc13189f5638cf08f1195eac2965a78ceb';

        $association1 = (object)[
            'ID' => $ID1,
            'className' => $associationKey1,
            'associatedID' => $associatedID1,
        ];

        $association2_1 = (object) [
            'ID' => $ID2,
            'className' => $associationKey2,
            'associatedID' => $associatedID2_1,
        ];

        $association2_2 = (object) [
            'ID' => $ID2,
            'className' => $associationKey2,
            'associatedID' => $associatedID2_1,
        ];

        CBModelAssociations::delete(
            $ID1
        );

        CBModelAssociations::delete(
            $ID2
        );

        /* fetch associations for ID with no associations */
        {
            $associations = CBModelAssociations::fetch(
                $ID1
            );

            $expectedAssociations = [];

            if ($associations != $expectedAssociations) {
                return CBTest::resultMismatchFailure(
                    'fetch associations for ID with no associations',
                    $associations,
                    $expectedAssociations
                );
            }
        }
        /* fetch associations for ID with no associations */


        CBModelAssociations::add(
            $ID1,
            $associationKey1,
            $associatedID1
        );

        CBModelAssociations::add(
            $ID2,
            $associationKey2,
            $associatedID2_1
        );

        CBModelAssociations::add(
            $ID2,
            $associationKey2,
            $associatedID2_2
        );

        $associations = CBModelAssociations::fetch($ID1);

        /* associations count 1 */
        {
            $actualCount = count($associations);
            $expectedCount = 1;

            if ($actualCount !== $expectedCount) {
                return CBTest::resultMismatchFailure(
                    'associations count 1',
                    $actualCount,
                    $expectedCount
                );
            }
        }
        /* associations count 1 */


        /* associations check 1 */
        {
            if ($associations[0] != $association1) {
                return CBTest::resultMismatchFailure(
                    'assocations check 1',
                    $associations[0],
                    $association1
                );
            }
        }
        /* associations check 1 */


        $associations = CBModelAssociations::fetch($ID2);

        /* associations count 2 */
        {
            $actualCount = count($associations);
            $expectedCount = 2;

            if ($actualCount !== $expectedCount) {
                return CBTest::resultMismatchFailure(
                    'associations count 2',
                    $actualCount,
                    $expectedCount
                );
            }
        }
        /* associations count 2 */


        /* associations check 2_1 */
        {
            if (!in_array($association2_1, $associations)) {
                return CBTest::resultMismatchFailure(
                    'assocations check 2_1',
                    $associations,
                    $association2_1
                );
            }
        }
        /* associations check 2_1 */


        /* associations check 2_2 */
        {
            if (!in_array($association2_2, $associations)) {
                return CBTest::resultMismatchFailure(
                    'assocations check 2_2',
                    $associations,
                    $association2_2
                );
            }
        }
        /* associations check 2_1 */


        $associations = CBModelAssociations::fetch(
            [
                $ID1,
                $ID2,
            ]
        );


        /* associations count 3 */
        {
            $actualCount = count($associations);
            $expectedCount = 3;

            if ($actualCount !== $expectedCount) {
                return CBTest::resultMismatchFailure(
                    'associations count 3',
                    $actualCount,
                    $expectedCount
                );
            }
        }
        /* associations count 3 */


        CBModelAssociations::delete(
            $ID1
        );

        CBModelAssociations::delete(
            $ID2
        );


        $associations = CBModelAssociations::fetch(
            [
                $ID1,
                $ID2,
            ]
        );


        /* associations count 4 */
        {
            $actualCount = count($associations);
            $expectedCount = 0;

            if ($actualCount !== $expectedCount) {
                return CBTest::resultMismatchFailure(
                    'associations count 4',
                    $actualCount,
                    $expectedCount
                );
            }
        }
        /* associations count 4 */


        return (object)[
            'succeeded' => true,
        ];
    }
    /* CBTest_general() */
}

<?php

final class
CBModelCacheTests {

    /* -- CBTest interfaces -- -- -- -- -- */



    /**
     * @return [object]
     */
    static function CBTest_getTests(): array {
        return [
            (object)[
                'name' => 'fetchModelsByID',
                'type' => 'server',
            ],
            (object)[
                'name' => 'general',
                'type' => 'server',
            ],
            (object)[
                'name' => 'save',
                'type' => 'server',
            ],
        ];
    }
    /* CBTest_getTests() */



    /* -- tests -- -- -- -- -- */



    /**
     * @return object
     */
    static function CBTest_fetchModelsByID(): stdClass {
        /* test 1 */

        $actualResult = CBModelCache::fetchModelsByID([]);
        $expectedResult = [];

        if ($actualResult !== $expectedResult) {
            return CBTest::resultMismatchFailure(
                'test 1',
                $actualResult,
                $expectedResult
            );
        }

        /* test 2 */

        $sortedRandomlyGeneratedIDs = [
            '1b15ca3d2c3cc8b093565008a650167d4f3aadeb',
            '4d3da1a1b8365097567c049cfb1dd913f500f4bc',
        ];

        CBDB::transaction(
            function () use ($sortedRandomlyGeneratedIDs) {
                CBModels::deleteByID($sortedRandomlyGeneratedIDs);
            }
        );

        $specs = [
            (object)[
                'className' => 'CBMessageView',
                'ID' => $sortedRandomlyGeneratedIDs[0],
            ],
            (object)[
                'className' => 'CBMessageView',
                'ID' => $sortedRandomlyGeneratedIDs[1],
            ],
        ];

        CBDB::transaction(
            function () use ($specs) {
                CBModels::save($specs);
            }
        );

        $models = CBModelCache::fetchModelsByID($sortedRandomlyGeneratedIDs);

        /* test 2: count */

        $actualResult = count($models);
        $expectedResult = 2;

        if ($actualResult !== $expectedResult) {
            return CBTest::resultMismatchFailure(
                'test 2: count',
                $actualResult,
                $expectedResult
            );
        }

        /* test 2: IDs */

        $actualResult = array_values(array_map(
            function ($model) {
                return $model->ID;
            },
            $models
        ));

        sort($actualResult);

        $expectedResult = $sortedRandomlyGeneratedIDs;

        if ($actualResult !== $expectedResult) {
            return CBTest::resultMismatchFailure(
                'test 2: IDs',
                $actualResult,
                $expectedResult
            );
        }

        /* test 2: clean up */

        CBDB::transaction(
            function () use ($sortedRandomlyGeneratedIDs) {
                CBModels::deleteByID($sortedRandomlyGeneratedIDs);
            }
        );

        /* all tests succeeded */

        return (object)[
            'succeeded' => true,
        ];
    }
    /* CBTest_fetchModelsByID() */



    /**
     * @return object
     */
    static function
    general(
    ): stdClass {
        CBDB::transaction(
            function () {
                $ID = CBID::generateRandomCBID();

                $model = CBModelCache::modelByID(
                    $ID
                );

                if (
                    !empty($model)
                ) {
                    throw new Exception(
                        'Model appears to be cached when it should not be.'
                    );
                }

                $model = CBModelCache::fetchModelByID(
                    $ID
                );

                if (
                    !empty($model)
                ) {
                    throw new Exception(
                        'Model appears to exist when it should not.'
                    );
                }

                $spec = (object)[
                    'className' => 'CBTextView2',
                    'ID' => $ID,
                ];

                CBModels::save(
                    $spec
                );

                $model = CBModelCache::fetchModelByID(
                    $ID
                );

                if (
                    !isset($model->className) ||
                    $model->className !== 'CBTextView2'
                ) {
                    throw new Exception(
                        'The model does not appear to have been fetched properly.'
                    );
                }

                $model = CBModelCache::modelByID(
                    $ID
                );

                if (
                    !isset($model->className) ||
                    $model->className !== 'CBTextView2'
                ) {
                    throw new Exception(
                        'The model does not appear to have been cached properly.'
                    );
                }

                CBModelCache::uncacheByID(
                    [$ID]
                );

                $model = CBModelCache::modelByID(
                    $ID
                );

                if (
                    !empty($model)
                ) {
                    throw new Exception(
                        'The model does not appear to have been uncached properly.'
                    );
                }

                CBModelCache::fetchModelLazilyByID(
                    $ID
                );

                $model = CBModelCache::fetchModelByID(
                    CBID::generateRandomCBID()
                );

                if (
                    !empty($model)
                ) {
                    throw new Exception(
                        'The false model appears to exist.'
                    );
                }

                $model = CBModelCache::modelByID(
                    $ID
                );

                if (
                    !isset($model->className) ||
                    $model->className !== 'CBTextView2'
                ) {
                    throw new Exception(
                        'The model does not appear to have been cached as a needed model properly.'
                    );
                }

                return 'CBDB_transaction_rollback';
            }
        );

        return (object)[
            'succeeded' => true,
        ];
    }
    /* general() */



    /**
     * @return object
     */
    static function
    save(
    ): stdClass {
        $ID = '275097249aeb4eb28b1f6bfa3280485346eb5c94';

        CBDB::transaction(
            function () use (
                $ID
            ) {
                CBModels::deleteByID(
                    $ID
                );
            }
        );

        $model = CBModelCache::modelByID(
            $ID
        );

        if (
            !empty($model)
        ) {
            throw new Exception(
                'Subtest 1: The model should not be cached.'
            );
        }

        $spec = (object)[
            'className' => 'CBMessageView',
            'ID' => $ID,
        ];

        CBDB::transaction(
            function () use (
                $spec
            ) {
                CBModels::save(
                    $spec
                );
            }
        );


        /* get model from cache */

        $model = CBModelCache::modelByID(
            $ID
        );

        if (
            !empty($model)
        ) {
            throw new Exception(
                'Subtest 2: The model should not be cached.'
            );
        }

        $model = CBModelCache::fetchModelByID(
            $ID
        );

        if (
            $model->ID !== $ID
        ) {
            throw new Exception(
                'Subtest 3: The model was not fetched.'
            );
        }

        $model = CBModelCache::modelByID(
            $ID
        );

        if (
            empty($model)
        ) {
            throw new Exception(
                'Subtest 4: The model was not cached.'
            );
        }

        /* update version after first save */

        $spec->version = 1;


        /* save again */

        CBDB::transaction(
            function () use (
                $spec
            ) {
                CBModels::save(
                    $spec
                );
            }
        );

        $model = CBModelCache::modelByID(
            $ID
        );

        if (
            !empty($model)
        ) {
            throw new Exception(
                CBConvert::stringToCleanLine(<<<EOT

                    Subtest 5: The cached model was not uncached when the model
                    was saved.

                EOT)
            );
        }

        $model = CBModelCache::fetchModelByID(
            $ID
        );

        if (
            $model->ID !== $ID
        ) {
            throw new Exception(
                'Subtest 6: The model was not fetched.'
            );
        }

        $model = CBModelCache::modelByID(
            $ID
        );

        if (
            empty($model)
        ) {
            throw new Exception(
                'Subtest 7: The model was not cached.'
            );
        }

        CBDB::transaction(
            function () use (
                $ID
            ) {
                CBModels::deleteByID(
                    $ID
                );
            }
        );

        $model = CBModelCache::modelByID(
            $ID
        );

        if (
            !empty($model)
        ) {
            throw new Exception(
                CDConvert::stringToCleanLine(<<<EOT

                    Subtest 8: The cached model was not uncached when the model
                    was deleted.

                EOT)
            );
        }

        return (object)[
            'succeeded' => true,
        ];
    }
    /* save() */

}

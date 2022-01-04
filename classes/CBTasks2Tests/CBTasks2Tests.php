<?php

final class
CBTasks2Tests {

    /* -- CBTest interfaces -- -- -- -- -- */



    /**
     * @return [object]
     */
    static function
    CBTest_getTests(
    ): array {
        return [
            (object)[
                'name' => 'runSpecificTask',
                'type' => 'server',
            ],
            (object)[
                'name' => 'exceptionHandling',
                'type' => 'server',
            ],
        ];
    }
    /* CBTest_getTests() */



    /* -- tests -- -- -- -- -- */



    /**
     * This test verifies two behaviors:
     *
     *      An exception thrown by a task should not be hidden.
     *
     *      After such an exception, the row in the CBTasks2 table should have a
     *      state of 4, meaning an error occurred when trying to run the task.
     *
     * @return object
     */
    static function
    exceptionHandling(
    ): stdClass {
        $CBID = 'add1d0e46582644d3b3488206d85bd3c22fdc19b';
        $actualExceptionMessage = '';
        $expectedExceptionMessage = 'CBTasks2Tests_testException';


        $CBIDAsSQL = CBID::toSQL(
            $CBID
        );

        CBDB::transaction(
            function () use (
                $CBID
            ) {
                CBModels::deleteByID(
                    $CBID
                );
            }
        );

        CBDB::transaction(
            function () use (
                $CBID
            ) {
                CBModels::save(
                    (object)[
                        'className' => 'CBMessageView',
                        'ID' => $CBID,
                        'markup' => 'throw an exception',
                    ]
                );
            }
        );

        try {
            CBTasks2::runSpecificTask(
                'CBTasks2Tests_task',
                $CBID
            );
        } catch (Throwable $throwable) {
            $actualExceptionMessage = $throwable->getMessage();
        }

        CBDB::transaction(
            function () use (
                $CBID
            ) {
                CBModels::deleteByID(
                    $CBID
                );
            }
        );

        if ($actualExceptionMessage != $expectedExceptionMessage) {
            return CBTest::resultMismatchFailure(
                'Subtest 1',
                $actualExceptionMessage,
                $expectedExceptionMessage
            );
        }

        $SQL = <<<EOT

            SELECT  state
            FROM    CBTasks2
            WHERE   className = 'CBTasks2Tests_task' AND
                    ID = {$CBIDAsSQL}

        EOT;

        $actualState = intval(CBDB::SQLToValue($SQL));
        $expectedState = 4; /* state value for error */

        if ($actualState != $expectedState) {
            return CBTest::resultMismatchFailure(
                'Subtest 2',
                $actualState,
                $expectedState
            );
        }

        CBTasks2::remove(
            'CBTasks2Tests_task',
            $CBID
        );

        return (object)[
            'succeeded' => true,
        ];
    }
    /* exceptionHandling() */



    /**
     * @return object
     */
    static function CBTest_runSpecificTask(): stdClass {
        CBTasks2Tests_task::deleteTestTask();
        CBTasks2Tests_task::addTestTask(3 /* completed */);

        $expectedResult = true;
        $actualResult = CBTasks2::runSpecificTask(
            'CBTasks2Tests_task',
            CBTasks2Tests_task::testTaskID()
        );

        if ($actualResult !== $expectedResult) {
            return CBTest::resultMismatchFailure(
                'Test 1',
                $actualResult,
                $expectedResult
            );
        }

        CBTasks2Tests_task::deleteTestTask();
        CBTasks2Tests_task::addTestTask(2 /* running */);

        $expectedResult = false;
        $actualResult = CBTasks2::runSpecificTask(
            'CBTasks2Tests_task',
            CBTasks2Tests_task::testTaskID()
        );

        if ($actualResult !== $expectedResult) {
            return CBTest::resultMismatchFailure(
                'Test 2',
                $actualResult,
                $expectedResult
            );
        }

        CBTasks2Tests_task::deleteTestTask();

        return (object)[
            'succeeded' => true,
        ];
    }
    /* CBTest_runSpecificTask() */

}
/* CBTasks2Tests */



/**
 *
 */
final class CBTasks2Tests_task {

    /**
     * @param int $state
     *
     * @return void
     */
    static function addTestTask(int $state): void {
        $classNameAsSQL = CBDB::stringToSQL(__CLASS__);
        $IDAsSQL = CBID::toSQL(CBTasks2Tests_task::testTaskID());
        $timestamp = time();
        $SQL = <<<EOT

            INSERT INTO CBTasks2
            (
                className,
                ID,
                state,
                timestamp
            )
            VALUES
            (
                {$classNameAsSQL},
                {$IDAsSQL},
                {$state},
                {$timestamp}
            )

        EOT;

        Colby::query($SQL);
    }
    /* addTestTask() */



    /**
     * @param ID $ID
     *
     * @return void
     */
    static function CBTasks2_run(string $ID): void {
        $model = CBModelCache::fetchModelByID($ID);
        $message = CBModel::valueToString($model, 'markup');

        if ($message === 'throw an exception') {
            throw new Exception('CBTasks2Tests_testException');
        }
    }
    /* CBTasks2_run() */



    /**
     * Deletes the test task row from the CBTasks2 table.
     *
     * @return void
     */
    static function deleteTestTask(): void {
        $classNameAsSQL = CBDB::stringToSQL(__CLASS__);

        $IDAsSQL = CBID::toSQL(
            CBTasks2Tests_task::testTaskID()
        );

        $SQL = <<<EOT

            DELETE FROM CBTasks2

            WHERE   className = {$classNameAsSQL} AND
                    ID = {$IDAsSQL}

        EOT;

        Colby::query($SQL);
    }
    /* deleteTestTask() */



    /**
     * @return CBID
     */
    static function testTaskID(): string {
        return '7e7909a13bb52cdcd3ecb531206a4acec50cd089';
    }

}
/* CBTasks2Tests_task */

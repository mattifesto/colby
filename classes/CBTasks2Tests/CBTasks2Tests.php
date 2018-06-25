<?php

/**
 *
 */
final class CBTasks2Tests {

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
    static function CBTest_exceptionHandling(): stdClass {
        $ID = 'add1d0e46582644d3b3488206d85bd3c22fdc19b';
        $IDAsSQL = CBHex160::toSQL($ID);
        $actualExceptionMessage = '';
        $expectedExceptionMessage = 'CBTasks2Tests_testException';

        CBModels::deleteByID($ID);

        CBModels::save((object)[
            'className' => 'CBMessageView',
            'ID' => $ID,
            'markup' => 'throw an exception',
        ]);

        try {
            CBTasks2::runSpecificTask('CBTasks2Tests_task', $ID);
        } catch (Throwable $throwable) {
            $actualExceptionMessage = $throwable->getMessage();
        }

        CBModels::deleteByID($ID);

        if ($actualExceptionMessage != $expectedExceptionMessage) {
            return CBTest::resultMismatchFailure('Subtest 1', $actualExceptionMessage, $expectedExceptionMessage);
        }

        $SQL = <<<EOT

            SELECT  state
            FROM    CBTasks2
            WHERE   className = 'CBTasks2Tests_task' AND
                    ID = {$IDAsSQL}

EOT;

        $actualState = intval(CBDB::SQLToValue($SQL));
        $expectedState = 4; /* state value for error */

        if ($actualState != $expectedState) {
            return CBTest::resultMismatchFailure('Subtest 2', $actualState, $expectedState);
        }

        return (object)[
            'succeeded' => true,
        ];
    }

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

    /**
     * @return [[<class>, <test>]]
     */
    static function CBUnitTests_tests(): array {
        return [
            ['CBTasks2', 'runSpecificTask'],
            ['CBTasks2', 'exceptionHandling'],
        ];
    }
}

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
        $IDAsSQL = CBHex160::toSQL(CBTasks2Tests_task::testTaskID());
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

    /**
     * Deletes the test task row from the CBTasks2 table.
     *
     * @return void
     */
    static function deleteTestTask(): void {
        $classNameAsSQL = CBDB::stringToSQL(__CLASS__);
        $IDAsSQL = CBHex160::toSQL(CBTasks2Tests_task::testTaskID());
        $SQL = <<<EOT

            DELETE FROM CBTasks2
            WHERE   className = {$classNameAsSQL} AND
                    ID = {$IDAsSQL}

EOT;

        Colby::query($SQL);
    }

    /**
     * @return ID
     */
    static function testTaskID(): string {
        return '7e7909a13bb52cdcd3ecb531206a4acec50cd089';
    }
}

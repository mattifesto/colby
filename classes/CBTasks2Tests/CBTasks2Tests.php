<?php

/**
 *
 */
final class CBTasks2Tests {

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
     * @return void
     */
    static function CBTasks2_run(): void {

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

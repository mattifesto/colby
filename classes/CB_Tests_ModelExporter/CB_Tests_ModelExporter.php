<?php

final class
CB_Tests_ModelExporter
{
    // -- CBTest interfaces



    /**
     * @return [object]
     */
    static function
    CBTest_getTests(
    ): array
    {
        $tests =
        [
            (object)
            [
                'type' =>
                'server',

                'name' =>
                'testModelThatDoesNotSupportExport',
            ],
        ];

        return $tests;
    }
    // CBTest_getTests()



    // -- tests



    /**
     * @return void
     */
    static function
    testModelThatDoesNotSupportExport(
    ): void
    {
        $testModel =
        CBModel::createSpec(
            'CB_FakeClassName'
        );

        $throwableSourceCBID =
        null;

        $expectedSourceCBID =
        'dee38e0031d9e90ba5717629abc447cab7d220d1';

        try
        {
            CB_ModelExporter::getDirectlyRequiredModelCBIDs(
                $testModel
            );
        }
        catch (
            Throwable $throwable
        ) {
            $throwableSourceCBID =
            CBException::throwableToSourceCBID(
                $throwable
            );

            if (
                $throwableSourceCBID !==
                $expectedSourceCBID
            ) {
                throw $throwable;
            }
        }

        if (
            $throwableSourceCBID !==
            $expectedSourceCBID
        ) {
            $cbmessage =
            CBTest::generateTestResultMismatchCBMessage(
                $throwableSourceCBID,
                $expectedSourceCBID
            );

            throw new CBException(
                'no exception was thrown',
                $cbmessage
            );
        }
    }
    // testModelThatDoesNotSupportExport()

}

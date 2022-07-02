<?php

final class
CB_Tests_YouTubeStatistics
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
                'name' =>
                'runGeneralTestCases',

                'type' =>
                'server',
            ],
        ];

        return $tests;
    }
    // CBTest_getTests()



    // -- tests



    /**
     * @return object
     */
    static function
    runGeneralTestCases(
    ): stdClass
    {
        $youtubeChannelModelCBIDs =
        CBModels::fetchCBIDsByClassName(
            'CB_YouTubeChannel'
        );

        $cbmessage = '';

        foreach(
            $youtubeChannelModelCBIDs as
            $youtubeChannelModelCBID
        ) {
            CB_Task_CollectYouTubeChannelStatistics::resetSessionStaticVariables();

            /* run task test */

            $testName =
            'run task test for YouTube channel model with CBID: ' .
            $youtubeChannelModelCBID;

            $actualValue =
            CBTasks2::runSpecificTask(
                'CB_Task_CollectYouTubeChannelStatistics',
                $youtubeChannelModelCBID
            );

            $expectedValue =
            true;

            if (
                $actualValue !== $expectedValue
            ) {
                $cbmessage =
                CBTest::generateTestResultMismatchCBMessage(
                    $actualValue,
                    $expectedValue
                );

                throw
                new CBException(
                    $testName,
                    $cbmessage,
                    'b258c7e884d046042e3aa8f19ad74de7643f9906'
                );
            }



            /* run count test */

            $testName =
            'run count test for YouTube channel model with CBID: ' .
            $youtubeChannelModelCBID;

            $actualValue =
            CB_Task_CollectYouTubeChannelStatistics::getSessionRunCount();

            $expectedValue =
            1;

            if (
                $actualValue !== $expectedValue
            ) {
                $cbmessage =
                CBTest::generateTestResultMismatchCBMessage(
                    $actualValue,
                    $expectedValue
                );

                throw
                new CBException(
                    $testName,
                    $cbmessage,
                    'c9bcf3854453a155a8fb8cee93360a9fc1eec341'
                );
            }



            /* statistics count test */

            $testName =
            'spec count test for YouTube channel model with CBID: ' .
            $youtubeChannelModelCBID;

            $sessionYouTubeStatisticsSpecs =
            CB_Task_CollectYouTubeChannelStatistics::getSessionYouTubeStatisticsSpecs();

            $actualValue =
            count(
                $sessionYouTubeStatisticsSpecs
            );

            $expectedValue =
            1;

            if (
                $actualValue !== $expectedValue
            ) {
                $cbmessage =
                CBTest::generateTestResultMismatchCBMessage(
                    $actualValue,
                    $expectedValue
                );

                throw
                new CBException(
                    $testName,
                    $cbmessage,
                    '973d6ac98c3b721c33623799651da20a64902162'
                );
            }



            /* statistics model test */

            $testName =
            'view count test for YouTube channel model with CBID: ' .
            $youtubeChannelModelCBID;

            $youtubeStatisticsSpec =
            $sessionYouTubeStatisticsSpecs[0];

            $youtubeStatisticsModelCBID =
            CBModel::getCBID(
                $youtubeStatisticsSpec
            );

            $youtubeStatisticsModel =
            CBModels::fetchModelByCBID(
                $youtubeStatisticsModelCBID
            );

            $statistics =
            CB_YouTubeStatistics::getStatistics(
                $youtubeStatisticsModel
            );

            $viewCount =
            CBModel::value(
                $statistics,
                'items.[0].statistics.viewCount'
            );

            if (
                $viewCount ===
                null
            ) {
                $cbmessage =
                CBTest::generateTestResultMismatchCBMessage(
                    $viewCount,
                    'any integer value'
                );

                throw
                new CBException(
                    $testName,
                    $cbmessage,
                    '0a5bda68d6f42735944d355ffeaa9f682b496fab'
                );
            }
        }
        // foreach channel


        $result =
        (object)[
            'succeeded' =>
            true,

            'message' =>
            $cbmessage,
        ];

        return $result;
    }
    // runGeneralTestCases()

}

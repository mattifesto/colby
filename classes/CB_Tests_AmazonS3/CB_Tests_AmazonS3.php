<?php

use Aws\S3\S3Client;
use Aws\Exception\AwsException;



/**
 * Matt Calkins
 * 2023-11-12
 *
 *      To run this test set the following environment variables before starting
 *      Apache in dev or define these variables in your composer file used to
 *      run a container.
 *
 *      export AWS_ACCESS_KEY_ID=<value>
 *      export AWS_SECRET_ACCESS_KEY=<value>
 *      export AWS_DEFAULT_REGION=<value>
 */
final class
CB_Tests_AmazonS3
{
    // -- CBTest interfaces



    /**
     * @return [object]
     */
    static function
    CBTest_getTests()
    : array
    {
        return [
            (object)[
                'name' => 'test1',
                'type' => 'server',
            ],
        ];
    }
    // CBTest_getTests()



    // -- tests



    /**
     * @return void
     */
    static function
    test1()
    : ?stdClass
    {
        /**
         * 2023-11-12
         * Matt Calkins
         *
         *      This API will not read the AWS_DEFAULT_REGION environment
         *      variable because that variable is only used by the CLI according
         *      to the following:
         *
         *          https://github.com/aws/aws-sdk-php/issues/2658
         *
         *      So we read the environment variable and use it.
         */

        $awsDefaultRegion =
        getenv('AWS_DEFAULT_REGION');

        if (
            $awsDefaultRegion === false
        ) {
            $returnValue =
            (object)
            [
                'succeeded' => 'skipped',
            ];

            return $returnValue;
        }

        $s3Client =
        new S3Client(
            [
                'region' =>
                $awsDefaultRegion,

                'version' =>
                '2006-03-01',
            ]
        );

        $buckets =
        $s3Client->listBuckets();

        foreach (
            $buckets['Buckets'] as $bucket
        ) {
            echo $bucket['Name'] . "\n";
        }
    }
    // test1

}

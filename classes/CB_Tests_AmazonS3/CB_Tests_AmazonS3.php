<?php

use Aws\S3\S3Client;
use Aws\Exception\AwsException;



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
    : void
    {
        $s3Client =
        new S3Client(
            [
                'profile' =>
                'colby-dev',

                'region' =>
                'us-west-2',

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

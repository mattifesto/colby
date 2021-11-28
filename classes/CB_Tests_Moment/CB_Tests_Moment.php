<?php

final class
CB_Tests_Moment {

    /* -- CBTest interfaces -- */



    /**
     * @return [object]
     */
    static function
    CBTest_getTests(
    ): array {
        return [
            (object)[
                'name' => 'build',
                'type' => 'server',
            ],
        ];
    }
    /* CBTest_getTests() */



    /* -- tests -- -- -- -- -- */



    /**
     * @return object
     */
    static function
    CBTest_build(
    ): stdClass {
        $momentModelCBID = '3d30657bf2e4264cc8fd6f3940919f2797c73ca2';
        $originalAuthorUserModelCBID = '2a5cc78ddc20b8a6bacf4cb5e41df591e79fa148';
        $originalText = 'foo bar baz';
        $originalCreatedTimestamp = time();

        $momentSpec = CBModel::createSpec(
            'CB_Moment',
            $momentModelCBID
        );


        CB_Moment::setAuthorUserModelCBID(
            $momentSpec,
            $originalAuthorUserModelCBID
        );

        CB_Moment::setCreatedTimestamp(
            $momentSpec,
            $originalCreatedTimestamp
        );

        CB_Moment::setText(
            $momentSpec,
            $originalText
        );


        $momentModel = CBModel::build(
            $momentSpec
        );

        $retreivedAuthorUserModelCBID = CB_Moment::getAuthorUserModelCBID(
            $momentModel
        );

        if (
            $retreivedAuthorUserModelCBID !== $originalAuthorUserModelCBID
        ) {
            return CBTest::resultMismatchFailure(
                'author user model CBID',
                $retreivedAuthorUserModelCBID,
                $originalAuthorUserModelCBID
            );
        }


        $retreivedCreatedTimestamp = CB_Moment::getCreatedTimestamp(
            $momentModel
        );

        if (
            $retreivedCreatedTimestamp !== $originalCreatedTimestamp
        ) {
            return CBTest::resultMismatchFailure(
                'created timestamp',
                $retreivedCreatedTimestamp,
                $originalCreatedTimestamp
            );
        }

        $retreivedText = CB_Moment::getText(
            $momentModel
        );

        if (
            $retreivedText !== $originalText
        ) {
            return CBTest::resultMismatchFailure(
                'text',
                $retreivedText,
                $originalText
            );
        }

        return (object)[
            'succeeded' => 'true',
        ];
    }
    /* CBTest_build() */

}

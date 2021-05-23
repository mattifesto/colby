<?php

final class
CBTest_CBTag {

    /* -- CBTest interfaces -- */



    /**
     * @return [object]
     */
    static function
    CBTest_getTests(
    ): array {
        return [
            (object)[
                'name' => 'stringToNormalizedTag',
                'type' => 'server',
            ],
        ];
    }
    /* CBTest_getTests() */



    /* -- tests -- */



    /**
     * @return object
     */
    static function
    CBTest_stringToNormalizedTag(
    ): stdClass {

        $originalStrings = [
            '',
            'dog',
            'DOG',
            'DOG😎🙏🏦2🍰3',
            ' haPPy ',
            'The Great Escape',
            'Jaws 3 In 3D 🌯',
            'CafÉ',
            'JalapeÑo',
            '2dog',
            '123',
            '1_2_3',
        ];

        $expectedNormalizedTags = [
            '',
            'dog',
            'dog',
            'dog23',
            'happy',
            'thegreatescape',
            'jaws3in3d',
            'café',
            'jalapeño',
            '2dog',
            '123',
            '1_2_3',
        ];

        for (
            $index = 0;
            $index < count($originalStrings);
            $index += 1
        ) {
            $originalString = $originalStrings[$index];
            $expectedNormalizedTag = $expectedNormalizedTags[$index];

            $actualNormalizedTag = CBTag::stringToNormalizedTag(
                $originalString
            );

            if ($actualNormalizedTag !== $expectedNormalizedTag) {
                return CBTest::resultMismatchFailure(
                    "Test Index {$index}",
                    $actualNormalizedTag,
                    $expectedNormalizedTag
                );
            }
        }

        return  (object)[
            'succeeded' => true,
        ];
    }
    /* CBTest_stringToNormalizedTag() */

}

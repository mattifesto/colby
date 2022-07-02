<?php

final class
CB_YouTube_Tests {

    /* -- CBTest interfaces -- -- -- -- -- */



    /**
     * @return [object]
     */
    static function
    CBTest_getTests(
    ): array {
        return [
            (object)[
                'name' => 'fetchRecentUploads',
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
    CBTest_fetchRecentUploads(
    ): stdClass {
        $value = CB_YouTube::fetchRecentUploads();

        $valueAsMessage = CBMessageMarkup::stringToMessage(
            CBConvert::valueToPrettyJSON(
                $value
            )
        );

        $cbmessage = <<<EOT

        Succeeded

        --- pre\n{$valueAsMessage}
        ---

        EOT;

        return (object)[
            'succeeded' => true,
            'message' => $cbmessage,
        ];
    }
    /* CBTest_fetchRecentUploads() */

}

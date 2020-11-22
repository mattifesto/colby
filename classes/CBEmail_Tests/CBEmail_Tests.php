<?php

final class CBEmail_Tests {

    /* -- CBTest interfaces -- -- -- -- -- */



    /**
     * @return [object]
     */
    static function CBTest_getTests(): array {
        return [
            (object)[
                'name' => 'sendCBMessage',
                'type' => 'server',
            ]
        ];
    }
    /* getTests() */



    /* -- tests -- -- -- -- -- */



    /**
     * @return object
     */
    static function CBTest_sendCBMessage(
    ): stdClass {
        $currentUserCBID = ColbyUser::getCurrentUserCBID();

        if ($currentUserCBID === null) {
            return (object)[
                'message' => 'No user is signed in.'
            ];
        }

        $currentUserModel = CBModels::fetchModelByIDNullable(
            $currentUserCBID
        );

        $currentUserEmail = CBModel::valueToString(
            $currentUserModel,
            'email'
        );

        if ($currentUserEmail === '') {
            return (object)[
                'message' => 'The current user does not have an email address.',
            ];
        }

        $currentUserFullName = CBModel::valueToString(
            $currentUserModel,
            'title'
        );

        $urlAsMessage = CBMessageMarkup::stringToMessage(
            cbsiteurl()
        );

        $cbmessage = <<<EOT

            This is an email from ($urlAsMessage (a $urlAsMessage)).

            This is a second paragraph.

            --- pre
            Computer Data
            -------------
            Compile
            Link
            F00D
            ---

        EOT;


        CBEmail::sendCBMessage(
            $currentUserEmail,
            $currentUserFullName,
            'CBTest_sendCBMessage',
            $cbmessage
        );

        return (object)[
            'succeeded' => true,
        ];
    }
    /* CBTest_sendCBMessage() */

}

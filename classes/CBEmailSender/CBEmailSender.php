<?php

final class CBEmailSender {

    /* -- CBInstall interfaces -- -- -- -- -- */



    /**
     * @return void
     */
    static function CBInstall_install(): void {
        CBModelUpdater::update(
            (object)[
                'className' => 'CBEmailSender',
                'ID' => CBEmailSender::websiteEmailSenderCBID(),
                'title' => 'Website Email Sender'
            ]
        );
    }
    /* CBInstall_install() */



    /**
     * @return [string]
     */
    static function CBInstall_requiredClassNames(): array {
        return [
            'CBModels',
        ];
    }



    /* -- CBModel interfaces -- -- -- -- -- */



    /**
     * @param object $spec
     *
     * @return object
     */
    static function CBModel_build(
        stdClass $spec
    ): stdClass {
        $SMTPServerHostname = trim(
            CBModel::valueToString(
                $spec,
                'SMTPServerHostname'
            )
        );

        $SMTPServerPort = CBModel::valueAsInt(
            $spec,
            'SMTPServerPort'
        ) ?? 25;

        if (
            !in_array($SMTPServerPort, [25, 465, 587, 2525])
        ) {
            throw new CBException(
                'The SMTP server port should be 25, 465, 587, or 2525.',
                '',
                'a8fcf61a471b6cb53d6d12f5d9c6a8103ca131f7'
            );
        }

        $SMTPServerSecurity = CBModel::valueToString(
            $spec,
            'SMTPServerSecurity'
        );

        $SMTPServerUsername = trim(
            CBModel::valueToString(
                $spec,
                'SMTPServerUsername'
            )
        );

        $SMTPServerPassword = trim(
            CBModel::valueToString(
                $spec,
                'SMTPServerPassword'
            )
        );

        $sendingEmailAddress = CBModel::valueAsEmail(
            $spec,
            'sendingEmailAddress'
        );

        $sendingEmailFullName = trim(
            CBModel::valueToString(
                $spec,
                'sendingEmailFullName'
            )
        );

        return (object)[
            'SMTPServerHostname' => $SMTPServerHostname,
            'SMTPServerPort' => $SMTPServerPort,
            'SMTPServerSecurity' => $SMTPServerSecurity,
            'SMTPServerUsername' => $SMTPServerUsername,
            'SMTPServerPassword' => $SMTPServerPassword,
            'sendingEmailAddress' => $sendingEmailAddress,
            'sendingEmailFullName' => $sendingEmailFullName,
        ];
    }
    /* CBModel_build() */



    /* -- functions -- -- -- -- -- */



    /**
     * @return CBID
     */
    static function websiteEmailSenderCBID(): string {
        return '244c25a0f46ac37d9a3845efed5574bd20064401';
    }

}

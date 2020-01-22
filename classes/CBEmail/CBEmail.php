<?php



final class CBEmail {

    /* -- CBAdmin interfaces -- -- -- -- -- */



    static function CBAdmin_getIssueMessages(): array {
        if (!file_exists(
            CBEmail::getSwiftmailerIncludeFilename()
        )) {
            return [
                <<<EOT

                    Swiftmailer is not installed in the correct location for
                    this website.

                EOT,
            ];
        }

        return [];
    }
    /* CBAdmin_getIssueMessages() */



    /* -- functions -- -- -- -- -- */



    /**
     * @return string
     */
    static function getSwiftmailerIncludeFilename(): string {
        return cbsitedir() . '/swiftmailer/lib/swift_required.php';
    }



    /**
     * @param string $HTMLContent
     *
     *      HTML that would go inside the <body> element of an HTML document.
     *
     * @return string
     *
     *      A string representing a full HTML document appropriate for sending
     *      as an email starting with <!doctype html>.
     */
    private static function HTMLContentToHTMLDocument(
        string $subject,
        string $HTMLContent
    ): string {
        ob_start();

        $bodyStyles = implode(
            '; ',
            [
                'font-family: \'Helvetica Neue\', Helvetica, Arial, sans-serif',
                'font-size: 18px',
                'padding: 20px'
            ]
        );

        $CBContentStyleSheet = file_get_contents(
            Colby::flexpath('CBContentStyleSheet', 'css', cbsysdir())
        );

        try {
            ?>

            <!doctype html>
            <html lang="en">
                <head>
                    <meta charset="UTF-8">
                    <title><?= cbhtml($subject) ?></title>
                    <meta name="description" content="">
                    <style>
                        * {
                            margin: 0;
                        }

                        <?= $CBContentStyleSheet ?>
                    </style>
                </head>
                <body class="CBContentStyleSheet" style="<?= $bodyStyles ?>">
                    <?= $HTMLContent ?>
                </body>
            </html>

            <?php
        } catch (Throwable $throwable) {
            ob_end_clean();

            throw $throwable;
        }

        return ob_get_clean();
    }
    /* HTMLContentToHTMLDocument() */



    /**
     * @param string $toEmail
     * @param string $toFullName
     * @param string $subject
     * @param string $cbmessage
     * @param object|null $cbemailsender
     *
     *      If not null, this should be a CBEmailSender model.
     *
     * @return void
     */
    static function sendCBMessage(
        string $toEmail,
        string $toFullName,
        string $subject,
        string $cbmessage,
        ?stdClass $cbemailsender = null
    ): void {
        if ($cbemailsender === null) {
            $cbemailsender = CBModelCache::fetchModelByID(
                CBEmailSender::websiteEmailSenderCBID()
            );

            /**
             * @TODO 2020_01_06
             *
             *      Better determine whether this is a functional model.
             */

            $SMTPServerHostname = CBModel::valueToString(
                $cbemailsender,
                'SMTPServerHostname'
            );

            if ($SMTPServerHostname === '') {
                $cbemailsender = null;
            }
        }

        if ($cbemailsender === null) {
            if (!defined('COLBY_EMAIL_SMTP_SERVER')) {
                throw new CBException(
                    'The system email has not been configured.'
                );
            }

            $cbemailsender = (object)[
                'SMTPServerHostname' => COLBY_EMAIL_SMTP_SERVER,
                'SMTPServerPort' => COLBY_EMAIL_SMTP_PORT,
                'SMTPServerSecurity' => COLBY_EMAIL_SMTP_SECURITY,
                'SMTPServerUsername' => COLBY_EMAIL_SMTP_USER,
                'SMTPServerPassword' => COLBY_EMAIL_SMTP_PASSWORD,
                'sendingEmailAddress' => COLBY_EMAIL_SENDER,
                'sendingEmailFullName' => COLBY_EMAIL_SENDER_NAME,
            ];
        }

        $SMTPTransport = Swift_SmtpTransport::newInstance(
            $cbemailsender->SMTPServerHostname,
            $cbemailsender->SMTPServerPort,
            $cbemailsender->SMTPServerSecurity
        );

        $SMTPTransport->setUsername(
            $cbemailsender->SMTPServerUsername
        );

        $SMTPTransport->setPassword(
            $cbemailsender->SMTPServerPassword
        );

        $mailer = Swift_Mailer::newInstance($SMTPTransport);

        $message = Swift_Message::newInstance();

        $message->setSubject($subject);

        $message->setFrom(
            [
                $cbemailsender->sendingEmailAddress =>
                $cbemailsender->sendingEmailFullName
            ]
        );

        $message->setTo(
            [
                $toEmail => $toFullName
            ]
        );

        $message->setBody(
            CBMessageMarkup::messageToText($cbmessage)
        );

        $message->addPart(
            CBEmail::HTMLContentToHTMLDocument(
                $subject,
                CBMessageMarkup::messageToHTML($cbmessage)
            ),
            'text/html'
        );

        $mailer->send($message);
    }
    /* sendCBMessage() */

}



$filename = CBEmail::getSwiftmailerIncludeFilename();

if (file_exists($filename)) {
    include_once($filename);
}
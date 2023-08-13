<?php

$filename = CBEmail::getSwiftmailerIncludeFilename();

if (file_exists($filename)) {
    include_once($filename);
}



final class
CBEmail
{
    /* -- CBAdmin interfaces -- -- -- -- -- */



    /**
     * @return [string]
     */
    static function
    CBAdmin_getIssueMessages(
    ): array
    {
        $cbmessages = [];

        $documentRootRelativeSubmoduleDirectoryPaths =
        CBGit::submodules();

        if (
            in_array(
                'swiftmailer',
                $documentRootRelativeSubmoduleDirectoryPaths
            )
        ) {
            $cbmessage =
            <<<EOT

                SwiftMailer exists as a submodule of this website. Once the site
                has been upgraded to use Colby as a PHP Composer library and all
                instances of the site have been verified to function properly
                remove this submodule.

            EOT;

            array_push(
                $cbmessages,
                $cbmessage
            );
        }

        return $cbmessages;
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
     * @param string $subject
     *
     *      The subject of the email is used as the title of the HTML document.
     *
     * @param string $HTMLContent
     *
     *      The HTML that would go inside the <body> element of the HTML
     *      document.
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
     * @param string $toEmailAddress
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
        string $toEmailAddress,
        string $toFullName,
        string $subject,
        string $cbmessage,
        ?stdClass $cbemailsender = null
    ): void {
        $messageAsPlaintext = CBMessageMarkup::messageToText(
            $cbmessage
        );

        $messageAsHTMLDocument = CBEmail::HTMLContentToHTMLDocument(
            $subject,
            CBMessageMarkup::messageToHTML($cbmessage)
        );

        CBEmail::sendTextAndHTML(
            $toEmailAddress,
            $toFullName,
            $subject,
            $messageAsPlaintext,
            $messageAsHTMLDocument,
            $cbemailsender
        );
    }
    /* sendCBMessage() */



    /**
     * @param string $toEmailAddress
     * @param string $toFullName
     * @param string $subject
     * @param string $messageAsPlaintext
     * @param string $messageAsHTMLDocument
     *
     *      The full HTML document for the message. Should start with
     *      <!doctype html>.
     *
     * @param object|null $cbemailsender
     *
     *      If not null, this should be a CBEmailSender model.
     *
     * @return void
     */
    static function sendTextAndHTML(
        string $toEmailAddress,
        string $toFullName,
        string $subject,
        string $messageAsPlaintext,
        string $messageAsHTMLDocument,
        ?stdClass $cbemailsender = null
    ): void {
        if ($cbemailsender === null) {
            $cbemailsender = CBModelCache::fetchModelByCBID(
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
            throw new CBException(
                'The system email has not been configured.'
            );
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
                $toEmailAddress => $toFullName
            ]
        );

        $message->setBody(
            $messageAsPlaintext
        );

        $message->addPart(
            $messageAsHTMLDocument,
            'text/html'
        );

        $mailer->send($message);
    }
    /* sendTextAndHTML() */

}

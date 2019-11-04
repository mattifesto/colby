<?php

final class CBErrorHandler {

    /* -- CBHTMLOutput interfaces -- -- -- -- -- */



    /**
     * @return [string]
     */
    static function CBHTMLOutput_JavaScriptURLs(): array {
        return [
            Colby::flexpath(__CLASS__, 'v544.js', cbsysurl()),
        ];
    }



    /**
     * @return [string]
     */
    static function CBHTMLOutput_requiredClassNames(): array {
        return [
            'CBUIPanel',
            'Colby',
        ];
    }



    /* -- functions -- -- -- -- -- */



    /**
     * This is the default exception handler.
     *
     * This function used as the parameter when set_exception_handler() is
     * called from Colby::initialize().
     *
     * As of 2019_08_04, CBHTMLOutput uses a custom exception handler that
     * follows the guidelines specified here. However, it will move to a render
     * function with a callback that uses a try block to catch exceptions which
     * is the current recommended way of handling exceptions.
     *
     * Custom exception handlers are no longer recommended because they can
     * easily be overridden by other custom exception handlers or try blocks
     * making it difficult at times to find out why the custom exception handler
     * was not used.
     *
     * Having said that, custom exception handlers should:
     *
     *      1. Call CBErrorHandler::report() passing the error or exception that
     *      occurred.
     *
     *      2. Within a try block perform a short attempt at completing the
     *      request with an error notification instead of the original requested
     *      content.
     *
     *      3. The catch of the try block should only call
     *      CBErrorHandler::report() with the inner error or exception.
     *
     * @TODO 2019_08_04
     *
     *      Documentation for handling exceptions should be converted to a
     *      documentation page that will be available in the documentation admin
     *      area.
     *
     * @param Throwable $throwable
     *
     * @return void
     */
    static function handle(Throwable $throwable): void {
        CBErrorHandler::report($throwable);

        try {
            CBErrorHandler::renderErrorReportPage($throwable);
        } catch (Throwable $innerThrowable) {
            CBErrorHandler::report($innerThrowable);
        }
    }
    /* handle() */



    /**
     * This function renders an HTML page to display an error message. It is
     * meant to be called only by an exception handler.
     *
     * See CBErrorHandler::handle() for additional documentation.
     *
     * This function attempts to render the error page assuming that the system
     * is in good working order and the exception that occurred was specific to
     * the code it was in.
     *
     * However, if an exception occurs when trying to render the page, this
     * function treats that as if there are serious system issues and renders
     * a basic error page and exits.
     *
     * @param Throwable $throwable
     *
     * @return void
     */
    static function renderErrorReportPage(Throwable $throwable): void {
        try {
            CBExceptionView::pushThrowable($throwable);

            $spec = CBModelTemplateCatalog::fetchLivePageTemplate();
            $spec->title = 'Error';
            $spec->sections = [
                (object)[
                    'className' => 'CBExceptionView',
                ],
            ];

            CBPage::renderSpec($spec);

            CBExceptionView::popThrowable();
        } catch (Throwable $innerThrowable) {
            CBErrorHandler::report($innerThrowable);

            CBErrorHandler::renderErrorReportPageForInnerErrorAndExit(
                $throwable,
                $innerThrowable
            );
        }
    }
    /* renderErrorReportPage() */



    /**
     * This function called to render an error page in cases where an original
     * exception occurred and during the process of trying to render an error
     * page another exception occurred.
     *
     *
     * @param Throwable $originalThrowable
     * @param Throwable $innerThrowable
     *
     * @return void
     */
    static function renderErrorReportPageForInnerErrorAndExit(
        Throwable $originalThrowable,
        Throwable $innerThrowable
    ): void {
        try {
            while (ob_get_level() > 0) {
                ob_end_clean();
            }

            ?>

            <!doctype html>
            <html lang="en">
                <head>
                    <meta charset="UTF-8">
                    <title>Error</title>
                    <meta name="description" content="">
                    <meta
                        name="viewport"
                        content="width=device-width, initial-scale=1"
                    >
                    <style>
                        * {
                            margin: 0;
                        }
                        html {
                            -webkit-text-size-adjust: 100%;
                            -ms-text-size-adjust: 100%;
                        }
                        body {
                            padding: 40px 20px;
                            text-align: center;
                        }
                    </style>
                </head>
                <body>
                    An error has occurred.
                </body>
            </html>

            <?php
        } catch (Throwable $innerInnerException) {
            /* this is a severe situation */
            CBErrorHandler::report($innerInnerException);
        } finally {
            exit;
        }
    }
    /* renderErrorReportPageForInnerError() */



    /**
     * This function:
     *
     *      - Makes the best record and notification of an exception as it can.
     *      - Should be the only function called to report exceptions.
     *      - Will never throw another exception.
     *      - Does nothing to react to an exception.
     *
     * This function is responsible for reporting an exception in various ways
     * if they are available. For instance if emails services are available and
     * the system is set up to email exceptions to an administrator, this
     * function will send that email.
     *
     * This function can be called from anywhere to report an exception which
     * is useful when code catches an exception it wants to report but doesn't
     * want to re-throw.
     *
     * NOTE:
     *
     *      This function has been revised many times. It is the only official
     *      non-exception throwing function in Colby. It is engineered with
     *      great effort to communicate as much as possible while still being
     *      resistent to even the most harsh of critical web server situations.
     *
     *      Changes to this function should be well thought out and documented.
     *
     * To test this function insert exceptions and errors in its code and the
     * code of the functions it calls.
     *
     * @param Throwable $exception
     *
     * @return void
     */
    static function report(Throwable $throwable): void {
        try {
            try {
                $firstLine = (
                    'Error ' .
                    CBConvert::throwableToMessage($throwable)
                );

                $firstLineAsMarkup = CBMessageMarkup::stringToMarkup(
                    $firstLine
                );

                $stackTraceAsMarkup = CBMessageMarkup::stringToMarkup(
                    Colby::exceptionStackTrace($throwable)
                );

                if ($throwable instanceof CBException) {
                    $extendedMessage = $throwable->getExtendedMessage();
                    $messageAsMarkup = <<<EOT

                        {$firstLineAsMarkup}

                        --- dl
                            --- dt
                                extended message
                            ---
                            --- dd
                                {$extendedMessage}
                            ---

                            --- dt
                                stack trace
                            ---
                            --- dd
                                --- pre\n{$stackTraceAsMarkup}
                                ---
                            ---
                        ---

EOT;
                } else {
                    $messageAsMarkup = <<<EOT

                        {$firstLineAsMarkup}

                        --- dl
                            --- dt
                                stack trace
                            ---
                            --- dd
                                --- pre\n{$stackTraceAsMarkup}
                                ---
                            ---
                        ---

EOT;
                }
            } catch (Throwable $innerThrowable) {
                $message = $innerThrowable->getMessage();

                $firstLine =
                "INNER ERROR \"{$message}\" occurred when"
                . " CBErrorHandler::report() attempted to generate a message";

                $messageAsMarkup = $firstLine;
            }

            try {
                $serialNumber = CBLog::log((object)[
                    'className' => __CLASS__,
                    'message' => $messageAsMarkup,
                    'severity' => 3,
                ]);
            } catch (Throwable $innerThrowable) {
                $serialNumber = '';
                $message = $innerThrowable->getMessage();

                error_log(
                    "INNER ERROR \"{$message}\" occurred when " .
                    "CBErrorHandler::report() attempted to create a log " .
                    "entry AFTER {$firstLine} " .
                    '(Source ID: c25603f39b103992980a5f7643c14b5839a3ebf8)'
                );
            }

            try {
                $link = (
                    cbsiteurl() .
                    "/admin/?" .
                    "c=CBLogAdminPage&" .
                    "serialNumber={$serialNumber}"
                );

                CBSlack::sendMessage(
                    (object)[
                        'message' => "{$firstLine} <{$link}|link>",
                    ]
                );
            } catch (Throwable $innerThrowable) {
                $message = $innerThrowable->getMessage();

                error_log(
                    "INNER ERROR \"{$message}\" occurred when " .
                    "CBErrorHandler::report() attempted to send a Slack " .
                    "message AFTER {$firstLine} " .
                    '(Source ID: 86a1652a3438f6d5f61068c732ae62275acb46c4)'
                );
            }
        } catch (Throwable $innerThrowable) {
            try {
                $message = $innerThrowable->getMessage();

                error_log(
                    "INNER ERROR \"{$message}\" occurred inside " .
                    "CBErrorHandler::report() " .
                    '(Source ID: 4e4e71a29377d1709613e45b8d4c343616fc532f)'
                );
            } catch (Throwable $secondInnerThrowable) {
                /**
                 * Things are really bad if this point is reached. This catch is
                 * just a guarantee that this function will not throw another
                 * exception.
                 */
            }
        }
    }
    /* report() */

}
/* CBErrorHandler */

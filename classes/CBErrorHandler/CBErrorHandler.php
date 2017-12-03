<?php

final class CBErrorHandler {

    /**
     * This is the default exception handler and is the official documentation
     * for development of custom exception handlers for Colby.
     *
     * This function used as the parameter when set_exception_handler() is
     * called from Colby::initialize().
     *
     * Both CBHTMLOutput and CBAjaxResponse have custom exception handlers that
     * follow the guidelines specified here.
     *
     * Custom exception handlers should:
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
     * @param Throwable $throwable
     *
     * @return null
     */
    static function handle(Throwable $throwable) {
        Colby::reportException($throwable);

        try {
            CBErrorHandler::renderErrorReportPage($throwable);
        } catch (Throwable $innerThrowable) {
            Colby::reportException($innerThrowable);
        }
    }

    /**
     * This function renders a standard HTML page to report an error.
     *
     * @param Throwable $throwable
     *
     * @return void
     */
    static function renderErrorReportPage(Throwable $throwable): void {
        CBExceptionView::pushThrowable($throwable);

        CBPage::renderSpec((object)[
            'className' => 'CBViewPage',
            'title' => 'Something has gone wrong',
            'layout' => (object)[
                'className' => 'CBPageLayout',
                'CSSClassNames' => 'center',
            ],
            'sections' => [
                (object)[
                    'className' => 'CBExceptionView',
                ],
            ],
        ]);

        CBExceptionView::popThrowable();
    }

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
     * @return null
     */
    static function report(Throwable $throwable) {
        try {
            try {
                $firstLine = 'Error ' . CBConvert::throwableToMessage($throwable);
                $firstLineAsMarkup = CBMessageMarkup::stringToMarkup($firstLine);
                $stackTraceAsMarkup = CBMessageMarkup::stringToMarkup(Colby::exceptionStackTrace($throwable));
                $messageAsMarkup = "{$firstLineAsMarkup}\n\n--- pre\n{$stackTraceAsMarkup}\n---\n";
            } catch (Throwable $innerThrowable) {
                $message = $innerThrowable->getMessage();
                $firstLine = "INNER ERROR \"{$message}\" occurred when Colby::reportException() attempted to generate a message";
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
                error_log("INNER ERROR \"{$message}\" occurred when Colby::reportException() attempted to create a log entry AFTER {$firstLine}");
            }

            try {
                $link = cbsiteurl() . "/admin/page/?class=CBLogAdminPage&serialNumber={$serialNumber}";

                CBSlack::sendMessage((object)[
                    'message' => "{$firstLine} <{$link}|link>",
                ]);
            } catch (Throwable $innerThrowable) {
                $message = $innerThrowable->getMessage();
                error_log("INNER ERROR \"{$message}\" occurred when Colby::reportException() attempted to send a Slack message AFTER {$firstLine}");
            }
        } catch (Throwable $innerThrowable) {
            try {
                $message = $innerThrowable->getMessage();
                error_log("INNER ERROR \"{$message}\" occurred inside Colby::reportException()");
            } catch (Throwable $secondInnerThrowable) {
                /**
                 * Things are really bad if this point is reached. This catch is
                 * just a guarantee that this function will not throw another
                 * exception.
                 */
            }
        }
    }
}

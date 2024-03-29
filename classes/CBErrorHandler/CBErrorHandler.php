<?php

final class CBErrorHandler {

    /* -- CBHTMLOutput interfaces -- -- -- -- -- */



    /**
     * @return [string]
     */
    static function
    CBHTMLOutput_JavaScriptURLs(
    ): array {
        return [
            Colby::flexpath(
                __CLASS__,
                'v675.13.js',
                cbsysurl()
            ),
        ];
    }
    /* CBHTMLOutput_JavaScriptURLs() */



    /**
     * @return [string]
     */
    static function
    CBHTMLOutput_requiredClassNames(
    ): array {
        return [
            'CBAjax',
        ];
    }
    /* CBHTMLOutput_requiredClassNames() */



    /* -- functions -- -- -- -- -- */



    /**
     * This function is set as the exception handler in Colby::initialize().
     *
     * This is a generic exception handler and does not send a response. When
     * the desired response type is known another exception handler should be
     * set that will respond appropriately.
     *
     * CBHTMLOutput and CBAjax set different exception handlers.
     *
     * Having said that, exception handlers should:
     *
     *      1. Call CBErrorHandler::report() passing the error or exception that
     *      occurred.
     *
     *      2. Perform a short attempt at completing the request with an error
     *      notification instead of the original requested content.
     *
     *       3. If another exception is thrown it will be treated as a fatal
     *       error.
     *
     * @TODO 2019_08_04
     *
     *      Documentation for handling exceptions should be converted to a
     *      documentation page that will be available in the documentation admin
     *      area.
     *
     * @param Throwable $error
     *
     * @return void
     */
    static function handle(
        Throwable $error
    ): void {
        CBErrorHandler::report($error);
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
    static function renderErrorReportPage(
        Throwable $throwable
    ): void {
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
     * @NOTE 2019_12_09
     *
     *      This function no longer exits. If it throws an exception the current
     *      exception handler will handle it which is fine.
     *
     *
     * @param Throwable $firstError
     * @param Throwable $secondError
     *
     * @return void
     */
    static function renderErrorReportPageForInnerErrorAndExit(
        Throwable $firstError,
        Throwable $secondError
    ): void {
        while (ob_get_level() > 0) {
            ob_end_clean();
        }

        $isDeveloper = CBUserGroup::userIsMemberOfUserGroup(
            ColbyUser::getCurrentUserCBID(),
            'CBDevelopersUserGroup'
        );

        if ($isDeveloper) {
            $oneLineErrorReport1 = CBException::throwableToOneLineErrorReport(
                $firstError
            );

            $oneLineErrorReport2 = CBException::throwableToOneLineErrorReport(
                $secondError
            );

            $messageAsHTML = (
                '<p><i>This page was rendered by ' .
                __METHOD__ .
                '()</i>' .
                '<p><b>First Error</b>' .
                '<p><code>' .
                cbhtml($oneLineErrorReport1) .
                '</code>' .
                '<p><b>Second Error</b>' .
                '<p><code>' .
                cbhtml($oneLineErrorReport2) .
                '</code>'
            );
        } else {
            $messageAsHTML = cbhtml(
                'An error has occurred.'
            );
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
                <?= $messageAsHTML ?>
            </body>
        </html>

        <?php
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
    static function
    report(
        Throwable $throwable
    ): void {
        try {
            $currentThrowable = $throwable;
            $index = 0;

            while ($currentThrowable && $index < 10) {
                error_log(
                    CBConvert::throwableToMessage($currentThrowable) .
                    " | index {$index} | error log entry made in " .
                    __METHOD__ .
                    '()'
                );

                error_log(
                    CBConvert::throwableToStackTrace($currentThrowable) .
                    " | index {$index} | error log entry made in " .
                    __METHOD__ .
                    '()'
                );

                $currentThrowable = $currentThrowable->getPrevious();
                $index += 1;
            }
        } catch (Throwable $ignoredError) {
            error_log(
                '(1) INNER ERROR: ' .
                'Attempting to write stack traces for the original error ' .
                'to the error log threw the following inner error: ' .
                '"' . $ignoredError->getMessage() . '" ' .
                $ignoredError->getFile() . ' ' .
                $ignoredError->getLine() . ' ' .
                '(2) ORIGINAL ERROR: ' .
                '"' . $throwable->getMessage() . '" ' .
                $throwable->getFile() . ' ' .
                $throwable->getLine() . ' ' .
                '(3) Source CBID: 0fb61539ff2a86e7855866a45e29e22f2011c884. ' .
                'This error log entry was made in the method: ' .
                __METHOD__ .
                '()'
            );
        }

        try {
            $errorCount = 0;
            $errorReportAsCBMessage = '';
            $currentError = $throwable;

            while ($currentError) {
                $errorCount += 1;

                if ($errorCount > 1) {
                    $errorReportAsCBMessage .= <<<EOT

                        --- CBUI_title1
                        Error {$errorCount}
                        ---

                    EOT;
                }

                $errorReportAsCBMessage .= (
                    CBException::throwableToErrorReportAsCBMessage(
                        $currentError
                    )
                );

                $currentError = $currentError->getPrevious();
            }
        } catch (Throwable $ignoredError) {
            error_log(
                CBConvert::throwableToMessage($ignoredError) .
                ' | error log entry made in ' .
                __METHOD__ .
                '()'
            );
        }



        /* log */

        try {
            CBLog::log(
                (object)[
                    'sourceClassName' => __CLASS__,
                    'message' => $errorReportAsCBMessage,
                    'severity' => 3,
                    'sourceID' => CBException::throwableToSourceCBID(
                        $throwable
                    ),
                ]
            );
        } catch (Throwable $ignoredError) {
            error_log(
                CBConvert::throwableToMessage($ignoredError) .
                ' | error log entry made in ' .
                __METHOD__ .
                '()'
            );
        }



        /* slack */

        try {
            $oneLineErrorReport = CBException::throwableToOneLineErrorReport(
                $throwable
            );

            $logAdminPageURL = cbsiteurl() . "/admin/?c=CBLogAdminPage";

            CBSlack::sendMessage(
                (object)[
                    'message' => (
                        "{$oneLineErrorReport} <{$logAdminPageURL}|link>"
                    ),
                ]
            );
        } catch (Throwable $ignoredError) {
            error_log(
                CBConvert::throwableToMessage($ignoredError) .
                ' | error log entry made in ' .
                __METHOD__ .
                '()'
            );
        }
    }
    /* report() */



    /**
     * The purpose of this function is to generate plain text information to
     * help developers debug and fix issues with the website.
     *
     * @param Throwable $rootThrowable
     *
     * @return string
     *
     *      This function returns plain text containing a collection of plain
     *      text stack traces starting with the stack trace for the $throwable
     *      argument followed by the stack traces for each previous error.
     */
    static function
    throwableToPlainTextIteratedStackTrace(
        Throwable $throwable
    ): string {
        try {
            $plainTextIteratedStackTrace = '';
            $currentThrowable = $throwable;
            $index = 0;

            while ($currentThrowable && $index < 10) {
                if ($index > 0) {
                    $plainTextIteratedStackTrace .= "\n\n\n";
                }

                $plainTextIteratedStackTrace .= (
                    "----- Exception Index {$index} -----\n\n"
                );

                $plainTextIteratedStackTrace .= (
                    CBConvert::throwableToStackTrace(
                        $currentThrowable
                    )
                );

                $currentThrowable = $currentThrowable->getPrevious();
                $index += 1;
            }

            return $plainTextIteratedStackTrace;
        } catch (
            Throwable $ignoredError
        ) {
            return <<<EOT

                The CBErrorHandler::throwableToPlainTextIteratedStackTrace()
                threw an error.

            EOT;
        }
    }
    /* throwableToPlainTextIteratedStackTrace() */

}
/* CBErrorHandler */

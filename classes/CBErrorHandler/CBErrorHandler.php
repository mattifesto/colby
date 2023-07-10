<?php

final class
CBErrorHandler {

    /* -- CBHTMLOutput interfaces -- */



    /**
     * @return [string]
     */
    static function
    CBHTMLOutput_JavaScriptURLs(
    ): array {
        return [
            Colby::flexpath(
                __CLASS__,
                'v675.54.js',
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
            'CBConvert',
        ];
    }
    /* CBHTMLOutput_requiredClassNames() */



    /* -- functions -- */



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
    static function
    renderErrorReportPage(
        Throwable $originalError
    ): void {
        try {
            CBExceptionView::pushThrowable(
                $originalError
            );

            $spec = CBModelTemplateCatalog::fetchLivePageTemplate();
            $spec->title = 'Error';

            $spec->sections = [
                (object)[
                    'className' => 'CBExceptionView',
                ],
            ];

            CBPage::renderSpec(
                $spec
            );

            CBExceptionView::popThrowable();
        } catch (
            Throwable $innerError
        ) {
            CBErrorHandler::report(
                $innerError
            );

            CBErrorHandler::renderErrorReportPageForInnerErrorAndExit(
                $originalError,
                $innerError
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
     * @param Throwable $originalError
     * @param Throwable $innerError
     *
     * @return void
     */
    static function
    renderErrorReportPageForInnerErrorAndExit(
        Throwable $originalError,
        Throwable $innerError
    ): void {
        while (
            ob_get_level() > 0
        ) {
            ob_end_clean();
        }

        $originalErrorText =
        CB_ErrorTextGenerator::generateErrorText(
            $originalError
        );

        $errorFileBasename =
        CB_Timestamp::convertToISO8601(
            CB_Timestamp::now()
        );

        $absoluteErrorsDirectory =
        cb_document_root_directory() .
        '/errors';

        if (
           !is_dir($absoluteErrorsDirectory)
        ) {
            mkdir($absoluteErrorsDirectory);
        }

        $absoluteErrorFileFilepath =
        "$absoluteErrorsDirectory/{$errorFileBasename}.txt";

        file_put_contents(
            $absoluteErrorFileFilepath,
            $originalErrorText
        );

        $isDeveloper = CBUserGroup::userIsMemberOfUserGroup(
            ColbyUser::getCurrentUserCBID(),
            'CBDevelopersUserGroup'
        );

        if (
            $isDeveloper
        ) {
            $oneLineErrorReportsForOriginalError = cbhtml(
                implode(
                    "\n",
                    CBException::throwableToOneLineErrorReports(
                        $originalError
                    )
                )
            );

            $oneLineErrorReportsForInnerError = cbhtml(
                implode(
                    "\n",
                    CBException::throwableToOneLineErrorReports(
                        $innerError
                    )
                )
            );

            $method = __METHOD__;

            /**
             * 2023_06_16
             * Matt Calkins
             *
             *      I'm going over night because these errors are not communicating
             *      to me in the right way.
             *
             *      1. When the user is not a dev and there's no way to communicate
             *          the system should tell every user an error file has been
             *          created and an admin should look at it.
             *
             *      2. The error file should be text. And that should be the way
             *          every error is shown. Text in a <pre> if shown on a web page.
             *
             *      3. Call stacks should be very clear text like:
             *
             *          function 1 was called
             *          then function 2 was called
             *          then function 3 was called
             *          then function 4 was called
             *          in function 4 and exeption was thrown...
             *
             *          This text should say very clearly exactly what happened.
             *          Often call stacks are confusing and they shouldn't be any more.
             */
            $messageAsHTML = <<<EOT

                <p>This page was rendered by {$method}()

                <p>absolute error file filepath: $absoluteErrorFileFilepath



                <h3>Original Error Text</h3>

                <pre>$originalErrorText</pre>



                <h3>Original Error</h3>

                <pre>{$oneLineErrorReportsForOriginalError}</pre>



                <h3>Inner Error</h3>

                <pre>{$oneLineErrorReportsForInnerError}</pre>

            EOT;
        }
        else
        {
            /**
             * @TODO 20230709
             * Matt Calkins
             *
             *      This function should be changed to determine if we are
             *      in an okay situation to give more information. We don't
             *      because it might be public. But it raw development
             *      cases we should set an environment variable or something
             *      to indicate that we can say more.
             *
             *      We could also have the default message say what that
             *      environment variable is. It's okay because only a dev
             *      would be able to do that anyway.
             *
             *      "To see more set IM_A_DEV=1 in container."
             */

            $isInADevelopmentEnvironment =
            false;

            if (
                $isInADevelopmentEnvironment
            ) {
                $message =
                CBException::throwableToOneLineErrorReport(
                    $innerError
                );
            }
            else
            {
                $message =
                'An error has occurred. ' .
                'If you are a developer set ' .
                '$isInADevelopmentEnvironment to true';
            }

            $messageAsHTML =
            cbhtml(
                $message
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
                    }
                    pre {
                        line-height: 2;
                        white-space: break-spaces;
                    }
                </style>
            </head>
            <body>
                <?= $messageAsHTML ?>
            </body>
        </html>

        <?php
    }
    /* renderErrorReportPageForInnerErrorAndExit() */



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
            $errorDescription = implode(
                ' <-- ',
                CBException::throwableToOneLineErrorReports(
                    $throwable
                )
            );

            $logAdminPageURL = cbsiteurl() . "/admin/?c=CBLogAdminPage";

            CBSlack::sendMessage(
                (object)[
                    'message' => (
                        "{$errorDescription} <{$logAdminPageURL}|link>"
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

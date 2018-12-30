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
            CBErrorHandler::report($innerThrowable);
        }
    }

    /**
     * This function renders an HTML page to display an error message. It is
     * meant to be called only by an exception handler. This function may throw
     * an exception.
     *
     * See CBErrorHandler::handle() for additional documentation.
     *
     * This function makes two attempts to render an error message to make life
     * easier for developers. The first attempt uses a CBViewPage to render the
     * message on a page that looks similar to other pages on the site.
     *
     * While a developer is workig on the code for the site the first attempt
     * may fail, for instance if there is currently an issue with rendering the
     * site header or the site footer. In response to an inner exception this
     * function will make a second attempt to render a page to report the inner
     * exception using fewer code paths.
     *
     * If this second attempt to render fails, the second inner exception will
     * be thrown back to the exception handler that called this function and
     * that exception will be logged. In this case no page will be rendered.
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
            if (ColbyUser::currentUserIsMemberOfGroup('Developers')) {
                $innerErrorMessage = 'INNER ERROR: ' .
                    CBConvert::throwableToMessage($innerThrowable);
                $errorMessage = 'ORIGINAL ERROR: ' .
                    CBConvert::throwableToMessage($throwable);
                $errorStackTrace = CBConvert::throwableToStackTrace($throwable);
            } else {
                $innerErrorMessage = 'Sorry, something has gone wrong. An ' .
                    'error occurred on this page and our administrators have ' .
                    'been notified.';
                $errorMessage = '';
                $errorStackTrace = '';
            }

            $CSS = <<<EOT

                body {
                    align-items: center;
                    display: flex;
                    flex-direction: column;
                    justify-content: center;
                }

                body > * {
                    box-sizing: border-box;
                    max-width: 100%;
                    padding: 20px;
                }

                .stack {
                    overflow-x: auto;
                    white-space: pre-line;
                }

EOT;

            CBHTMLOutput::reset();
            CBHTMLOutput::addCSS($CSS);
            CBHTMLOutput::begin();

            $info = CBHTMLOutput::pageInformation();
            $info->classNameForPageSettings = 'CBPageSettingsForResponsivePages';
            $info->title = "Error";

            ?>

            <div><?= cbhtml($innerErrorMessage) ?></div>
            <div><?= cbhtml($errorMessage) ?></div>
            <div class="stack"><?= cbhtml($errorStackTrace) ?></div>

            <?php

            CBHTMLOutput::render();
        }
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
     * @return void
     */
    static function report(Throwable $throwable): void {
        try {
            try {
                $firstLine = 'Error ' . CBConvert::throwableToMessage($throwable);
                $firstLineAsMarkup = CBMessageMarkup::stringToMarkup($firstLine);
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

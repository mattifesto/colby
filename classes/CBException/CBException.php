<?php

/**
 * A CBException allows developers to throw exceptions with a longer CBMessage
 * and a source ID.
 *
 *      $exception->getMesssage()
 *
 *          Returns the same type of text string as any other exception.
 *
 *      $exception->getCBMessage()
 *
 *          Returns a CBMessage string that replaces the text string returned by
 *          getMessage(). In cases where a CBMessage would add nothing, it will
 *          be set to an empty string which indicates that the text exception
 *          message should be used.
 *
 *      Which to display?
 *
 *          If the CBMessage is a non-empty string and the UI situation
 *          can display a full message, use the CBMessage.
 *
 *          Otherwise, use the text exception message.
 */
 class CBException extends Exception {

    private $cbmessage;
    private $sourceCBID;


    /* -- constructor -- -- -- -- -- */

    /**
     * @param string $message
     *
     *      A plain text message.
     *
     * @param string $cbmessage
     *
     *      A message formatted with message markup.
     *
     * @param string $sourceCBID
     *
     *      An ID (160-bit hexadecimal number) indicating the source of the
     *      exception.
     *
     * @param int $code (optional)
     * @param Throwable $previous (optional)
     *
     * @return CBException
     */
    public function __construct(
        string $message,
        string $cbmessage,
        ?string $sourceCBID = null,
        int $code = 0,
        Throwable $previous = null
    ) {
        parent::__construct($message, $code, $previous);

        $this->cbmessage = $cbmessage;
        $this->sourceCBID = $sourceCBID;
    }



    /* -- CBHTMLOutput interfaces -- -- -- -- -- */



    /**
     * @return [string]
     */
    static function CBHTMLOutput_JavaScriptURLs(): array {
        return [
            Colby::flexpath(__CLASS__, 'v512.js', cbsysurl()),
        ];
    }
    /* CBHTMLOutput_JavaScriptURLs() */



    /**
     * @return [string]
     */
    static function CBHTMLOutput_requiredClassNames(): array {
        return [
            'CBConvert',
            'CBMessageMarkup',
            'CBModel',
            'Colby',
        ];
    }
    /* CBHTMLOutput_requiredClassNames() */



    /* -- functions -- -- -- -- -- */



    /**
     * @deprecated use CBExceptionWithValue
     *
     * @param string $title
     * @param mixed $value
     * @param CBID $sourceCBID
     *
     * @return CBException
     */
    static function createModelIssueException(
        string $title,
        $value,
        ?string $sourceCBID = null
    ): CBException {
        return CBException::createWithValue(
            $title,
            $value,
            $sourceCBID
        );
    }
    /* createModelIssueException() */



    /**
     * @deprecated use CBExceptionWithValue
     */
    static function createWithValue(
        string $title,
        $value,
        ?string $sourceCBID = null
    ): CBException {
        $titleAsCBMessage = CBMessageMarkup::stringToMessage(
            $title
        );

        $valueAsCBMessage = CBMessageMarkup::stringToMessage(
            CBConvert::valueToPrettyJSON(
                $value
            )
        );

        $cbmessage = <<<EOT

            {$titleAsCBMessage}

            --- pre\n{$valueAsCBMessage}
            ---

        EOT;

        return new CBException(
            $title,
            $cbmessage,
            $sourceCBID
        );
    }
    /* createWithValue() */



    /**
     * @return string
     */
    function getCBMessage(): string {
        if ($this->cbmessage === '') {
            return CBMessageMarkup::stringToMessage(
                $this->getMessage()
            );
        } else {
            return $this->cbmessage;
        }
    }



    /**
     * @deprecated use cbexception->getCBMessage()
     */
    function getExtendedMessage(): string {
        return $this->getCBMessage();
    }




    /**
     * @return CBID|null
     */
    function getSourceCBID(): ?string {
        return $this->sourceCBID;
    }
    /* getSourceID() */



    /**
     * @deprecated use cbexception->getSourceCBID()
     */
    function getSourceID(): ?string {
        return $this->getSourceCBID();
    }
    /* getSourceID() */



    /**
     * @param Throwable $throwable
     *
     * @return string
     */
    static function throwableToCBMessage(
        Throwable $throwable
    ): string {
        if ($throwable instanceof CBException) {
            return $throwable->getCBMessage();
        } else {
            return '';
        }
    }
    /* throwableToCBMessage() */



    /**
     * This function returns an error report as a CBMessage for the $throwable
     * parameter. It does not in include the previous exception, if there is
     * one, in the report.
     *
     * Callers can call again to create more reports for previous errors.
     *
     * @param Throwable $throwable
     *
     * @return string
     */
    static function throwableToErrorReportAsCBMessage(
        Throwable $throwable
    ): string {
        $oneLineErrorReport = CBException::throwableToOneLineErrorReport(
            $throwable
        );

        $oneLineErrorReportAsCBMessage = CBMessageMarkup::stringToMessage(
            $oneLineErrorReport
        );

        $stackTraceAsCBMessage = CBMessageMarkup::stringToMessage(
            Colby::exceptionStackTrace($throwable)
        );

        if ($throwable instanceof CBException) {
            $extendedMessage = $throwable->getExtendedMessage();
            $errorReportAsCBMessage = <<<EOT

                {$oneLineErrorReportAsCBMessage}

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
                        --- pre\n{$stackTraceAsCBMessage}
                        ---
                    ---
                ---

            EOT;
        } else {
            $errorReportAsCBMessage = <<<EOT

                {$oneLineErrorReportAsCBMessage}

                --- dl
                    --- dt
                        stack trace
                    ---
                    --- dd
                        --- pre\n{$stackTraceAsCBMessage}
                        ---
                    ---
                ---

            EOT;
        }

        return $errorReportAsCBMessage;
    }
    /* throwableToErrorReportAsCBMessage() */



    /**
     * @param Throwable $throwable
     *
     * @return string
     */
    static function throwableToOneLineErrorReport(
        Throwable $throwable
    ): string {
        $message = $throwable->getMessage();
        $basename = basename($throwable->getFile());
        $line = $throwable->getLine();

        return "\"{$message}\" in {$basename} line {$line}";
    }
    /* throwableToOneLineErrorReport() */



    /**
     * @param Throwable $throwable
     *
     * @returh CBID|null
     */
    static function throwableToSourceCBID(
        Throwable $throwable
    ): ?string {
        if ($throwable instanceof CBException) {
            return $throwable->getSourceCBID();
        } else {
            return null;
        }
    }
    /* throwableToSourceID() */



    /**
     * @deprecated use CBException::throwableToSourceCBID()
     */
    static function throwableToSourceID(Throwable $throwable): ?string {
        return CBException::throwableToSourceCBID($throwable);
    }
    /* throwableToSourceID() */

}

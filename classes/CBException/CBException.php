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
final class CBException extends Exception {

    private $cbmessage;
    private $sourceID;


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
     * @param string $sourceID
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
        ?string $sourceID = null,
        int $code = 0,
        Throwable $previous = null
    ) {
        parent::__construct($message, $code, $previous);

        $this->cbmessage = $cbmessage;
        $this->sourceID = $sourceID;
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
     * @NOTE 2019_10_19
     *
     *      This function was created to make it easy to create exceptions
     *      related to a model that would also display the model in JSON.
     *      However, over time, it became a great function to create exceptions
     *      related to any kind of value that needs to be displayed in JSON.
     *
     *      Furthermore, passing an object with two or more properties can
     *      create an exception related to multiple models or any other kinds of
     *      values.
     *
     *      This function should be renamed, but as of now I'm not sure what the
     *      new name should be.
     *
     * @param $title
     *
     *      Text describing the issue with the model. The text should be
     *      understandable in situations where the developer can't yet see the
     *      model JSON.
     *
     *      Examples:
     *
     *          The "className" property on a cart item model is not set.
     *
     *          The "priceInCents" property on an SCProduct model should be set
     *          to an integer >= 0
     *
     *          CBModel::build() returned null for a cart item spec.
     *
     * @param mixed $value
     *
     * @return CBException
     */
    static function createModelIssueException(
        string $title,
        $value,
        ?string $sourceID = null
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
            $sourceID
        );
    }
    /* createModelIssueException() */



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
     * @deprecated use getCBMessage()
     *
     * @return string
     */
    function getExtendedMessage(): string {
        return $this->getCBMessage();
    }



    /**
     * @return string
     */
    function getSourceID(): ?string {
        return $this->sourceID;
    }
    /* getSourceID() */



    /**
     * @param Throwable $throwable
     *
     * @returh string
     */
    static function throwableToCBMessage(Throwable $throwable): string {
        if ($throwable instanceof CBException) {
            return $throwable->getCBMessage();
        } else {
            return '';
        }
    }
    /* throwableToCBMessage() */



    /**
     * @param Throwable $throwable
     *
     * @returh string|null
     */
    static function throwableToSourceID(Throwable $throwable): ?string {
        if ($throwable instanceof CBException) {
            return $throwable->getSourceID();
        } else {
            return null;
        }
    }
    /* throwableToSourceID() */

}

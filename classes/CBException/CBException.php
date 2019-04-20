<?php

/**
 * A CBException allows developers to throw exceptions with a longer extended
 * message formatted with message markup.
 *
 *      $exception->getMesssage()
 *
 *          Returns the same type of text string as any other exception.
 *
 *      $exception->getExtendedMessage()
 *
 *          Returns a string of message markup that replaces the text string
 *          returned by getMessage(). Use one or the other, depending on the
 *          situation, not both.
 */
final class CBException extends Exception {

    private $extendedMessage;
    private $sourceID;

    /* -- constructor -- -- -- -- -- */

    /**
     * @param string $message
     *
     *      A plain text message.
     *
     * @param string $extendedMessage
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
        string $extendedMessage,
        ?string $sourceID = null,
        int $code = 0,
        Throwable $previous = null
    ) {
        parent::__construct($message, $code, $previous);

        $this->extendedMessage = $extendedMessage;
        $this->sourceID = $sourceID;
    }

    /* -- CBHTMLOutput -- -- -- -- -- */

    /**
     * @return [string]
     */
    static function CBHTMLOutput_JavaScriptURLs(): array {
        return [
            Colby::flexpath(__CLASS__, 'v472.js', cbsysurl()),
        ];
    }

    /**
     * @return [string]
     */
    static function CBHTMLOutput_requiredClassNames(): array {
        return [
            'Colby',
        ];
    }

    /* -- functions -- -- -- -- -- */

    /**
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
     * @param mixed $model
     *
     *      This property is mixed in cases where the error may be caused
     *      because the model value is not an object.
     *
     * @return CBException
     */
    static function createModelIssueException(
        string $title,
        $model,
        ?string $sourceID = null
    ): CBException {
        $titleAsMessage = CBMessageMarkup::stringToMessage($title);
        $modelAsMessage = CBMessageMarkup::stringToMessage(
            CBConvert::valueToPrettyJSON(
                $model
            )
        );

        $message = <<<EOT

            {$titleAsMessage}

            --- pre\n{$modelAsMessage}
            ---

EOT;

        return new CBException($title, $message, $sourceID);
    }

    /**
     * @return string
     */
    function getExtendedMessage(): string {
        return $this->extendedMessage;
    }

    /**
     * @return string
     */
    function getSourceID(): ?string {
        return $this->sourceID;
    }
}

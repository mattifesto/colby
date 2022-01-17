<?php

class
CBExceptionWithValue
extends
CBException {

    /**
     * @param string $message
     *
     *      Text describing the issue with the value. The text should be
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
     *      This value should be convertible to JSON. The value may be a value
     *      that has an issue referred to by the title or a value that can
     *      highlight or give evidence to the assertion made by the title.
     *
     * @param ?CBID $sourceCBID (optional)
     * @param int code (optional)
     * @param Throwable $previous (optional)
     *
     * @return CBExceptionWithValue
     */
    function __construct(
        string $message,
        /* mixed */ $value,
        string $sourceCBID = null,
        int $code = 0,
        Throwable $previous = null
    ) {
        $messageAsCBMessage = CBMessageMarkup::stringToMessage(
            $message
        );

        $valueAsCBMessage = CBMessageMarkup::stringToMessage(
            CBConvert::valueToPrettyJSON(
                $value
            )
        );

        $cbmessage = <<<EOT

            {$messageAsCBMessage}

            --- pre\n{$valueAsCBMessage}
            ---

        EOT;

        parent::__construct(
            $message,
            $cbmessage,
            $sourceCBID,
            $code,
            $previous
        );
    }
    /* __construct() */

}

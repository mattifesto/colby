<?php

final class CBException extends Exception {

    private $extendedMessage;

    /**
     * @param string $message
     *
     *      A plain text message.
     *
     * @param string $extendedMessage
     *
     *      A message formatted with message markup.
     *
     * @param int $code (optional)
     * @param Throwable $previous (optional)
     *
     * @return CBException
     */
    public function __construct(
        string $message,
        string $extendedMessage,
        int $code = 0,
        Throwable $previous = null
    ) {
        parent::__construct($message, $code, $previous);

        $this->extendedMessage = $extendedMessage;
    }

    /**
     * @return string
     */
    function getExtendedMessage(): string {
        return $this->extendedMessage;
    }
}

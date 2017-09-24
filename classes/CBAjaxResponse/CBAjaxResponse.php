<?php

class CBAjaxResponse {
    private $isActive = true;
    private static $countOfSends = 0;

    public $className = 'CBAjaxResponse';
    public $message = '';
    public $wasSuccessful = false;

    /**
     * @return CBAjaxResponse
     */
    public function __construct() {
        set_exception_handler(array($this, 'handleException'));
    }

    /**
     * @return null
     */
    public function cancel() {
        if ($this->isActive) {
            $this->isActive = false;
            restore_exception_handler();
        }
    }

    /**
     * @see Colby::handleException()
     *
     *      This exception handler places all of its code in a try block. If
     *      anything goes wrong it will result in an inner exception which we
     *      attempt to note in the error log. An inner exception in an exception
     *      handler is a unrecoverable dead end which generally only occurs
     *      during development on the exception handler itself.
     *
     * @param Throwable $exception
     *
     * @return null
     */
    public function handleException($exception) {
        try {

            Colby::reportException($exception);

            $this->classNameForException = get_class($exception);
            $this->message = $exception->getMessage();
            $this->stackTrace = Colby::exceptionStackTrace($exception);
            $this->wasSuccessful = false;
            $this->send();

        } catch (Exception $innerException) {

            /**
             * 1. Create message with clear indication that this is an exception
             * handler inner exception.
             *
             * 2. Attempt to log the message.
             *
             * 3. Attempt to send the message to slack.
             */

            $message = CBConvert::throwableToMessage($innerException);
            $message = "CBAjaxResponse::handleException() INNER EXCEPTION: {$message}";
            CBLog::addMessage(__METHOD__, 2, $message);
            CBSlack::sendMessage((object)[
                'message' => $message,
            ]);

        }
    }

    /**
     * This function contains a lot of verification code because when a bug
     * occurs here it tends to be messy and developers will need as much
     * information as they can get to fix it.
     *
     * @return null
     */
    public function send() {
        if (!$this->isActive) {
            return;
        }

        if (!isset($this->warnings)) {
            $this->warnings = CBAjaxContext::warnings();
        }

        $output = json_encode($this);

        /**
         * There should be no errors after this point so we are free to
         * increment the send count. The first time should also be the last time
         * the send count is incremented.
         */

        CBAjaxResponse::$countOfSends += 1;

        if (CBAjaxResponse::$countOfSends > 1) {
            $count = CBAjaxResponse::$countOfSends;
            throw new Exception("The send count was incremented to {$count} for the output: {$output}");
        } else {
            header('Content-type: application/json');
        }

        echo $output;

        $this->cancel();
    }
}

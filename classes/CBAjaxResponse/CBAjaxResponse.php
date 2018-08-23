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
     * 2017.12.03 This function was updated to comply with the custom exception
     * handler documentation in the CBErrorHandler::handle() comments.
     *
     * @param Throwable $throwable
     *
     * @return null
     */
    public function handleException(Throwable $throwable) {
        CBErrorHandler::report($throwable);

        try {
            $this->classNameForException = get_class($throwable);
            $this->message = 'Error ' . CBConvert::throwableToMessage($throwable);
            $this->stackTrace = Colby::exceptionStackTrace($throwable);
            $this->wasSuccessful = false;
            $this->send();
        } catch (Throwable $innerThrowable) {
            CBErrorHandler::report($innerThrowable);
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

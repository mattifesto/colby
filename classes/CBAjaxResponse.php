<?php

class CBAjaxResponse {
    private $isActive = true;

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
     * @return null
     */
    public function handleException($exception) {
        try {
            Colby::reportException($exception);

            $this->classNameForException = get_class($exception);
            $this->message = $exception->getMessage();
            $this->stackTrace = Colby::exceptionStackTrace($exception);
        } catch (Exception $innerException) {
            $class = get_class($innerException);
            $message = $innerException->getMessage();
            $this->message = "CBAjaxResponse Inner {$class}: {$message}";
            $this->stackTrace = Colby::exceptionStackTrace($innerException);

            $file = $innerException->getFile();
            $line = $innerException->getLine();

            error_log("CBAjaxResponse Inner {$class}:  {$file}({$line}) {$message}");
        }

        $this->wasSuccessful = false;
        $this->send();
    }

    /**
     * @return null
     */
    public function send() {
        if (!$this->isActive) {
            return;
        }

        if (!isset($this->warnings)) {
            $this->warnings = CBAjaxContext::warnings();
        }

        header('Content-type: application/json');

        echo json_encode($this);

        $this->cancel();
    }
}

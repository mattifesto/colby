<?php

/**
 *
 */
class CBAjaxResponse
{
    private $isActive       = true;

    public $message         = '';
    public $wasSuccessful   = false;

    /**
     * @return CBAjaxResponse
     */
    public function __construct()
    {
        set_exception_handler(array($this, 'handleException'));
    }

    /**
     * @return void
     */
    public function cancel()
    {
        if ($this->isActive)
        {
            $this->isActive = false;

            restore_exception_handler();
        }
    }

    /**
     * @return void
     */
    public function handleException($exception)
    {
        try
        {
            Colby::reportException($exception);

            $class              = get_class($exception);
            $message            = $exception->getMessage();
            $this->message      = "{$class}: {$message}";
            $this->stackTrace   = Colby::exceptionStackTrace($exception);
        }
        catch (Exception $innerException)
        {
            $class              = get_class($innerException);
            $message            = $innerException->getMessage();
            $this->message      = "CBAjaxResponse Inner {$class}: {$message}";
            $this->stackTrace   = Colby::exceptionStackTrace($innerException);

            $file               = $innerException->getFile();
            $line               = $innerException->getLine();

            error_log("CBAjaxResponse Inner {$class}:  {$file}({$line}) {$message}");
        }

        $this->wasSuccessful = false;
        $this->send();
    }

    /**
     * @return void
     */
    public function send()
    {
        if (!$this->isActive)
        {
            return;
        }

        header('Content-type: application/json');

        echo json_encode($this);

        $this->cancel();
    }
}

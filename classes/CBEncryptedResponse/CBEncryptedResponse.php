<?php

/**
 *
 */
class CBEncryptedResponse
{
    const CountOfInitializationVectorBytes  = 16;
    const EncryptionMethod                  = 'aes-256-cbc';

    /**
     * Private instance variables used by the class.
     */

    private $isActive       = false;
    private $response       = null;

    /**
     * Public instance variables will be serialized to JSON, encrypted, and sent.
     */

    public $message         = null;
    public $wasSuccessful   = false;

    /**
     * If no shared secret has been set, attempting to create an instance of
     * this class will immediately send an error response and exit.
     *
     * @return CBEncryptedResponse
     */
    public function __construct()
    {
        $this->response                             = new stdClass();
        $this->response->schema                     = 'CBEncryptedResponse';
        $this->response->schemaVersion              = 1;
        $this->response->encryptedJSONBase64        = null;
        $this->response->initializationVectorBase64 = null;
        $this->response->message                    = null;

        if (file_exists(CBSiteDirectory . '/.htSharedSecret'))
        {
            $this->isActive = true;

            set_exception_handler(array($this, 'handleException'));
        }
        else
        {
            $this->response->message = 'No shared secret has been set for this website.';

            header('Content-type: application/json');

            echo json_encode($this->response);

            exit();
        }
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
     * @return string
     */
    public function encryptedJSONBase64($initializationVector)
    {
        $password               = file_get_contents(CBSiteDirectory . '/.htSharedSecret');
        $key                    = pack('H*', hash('sha256', $password));

        /**
         * TODO: Upgrade to PHP 5.4 and use:
         *
         * $key = hex2bin(hash('sha256', $password));
         */

         $JSON                  = json_encode($this);
         $encryptedJSONBase64   = openssl_encrypt($JSON,
                                                  self::EncryptionMethod,
                                                  $key,
                                                  0,
                                                  $initializationVector);

        return $encryptedJSONBase64;
    }

    /**
     * @return void
     */
    public function handleException($exception)
    {
        $this->cancel();

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
            $this->message      = "CBEncryptedResponse Inner {$class}: {$message}";
            $this->stackTrace   = Colby::exceptionStackTrace($innerException);

            $file               = $innerException->getFile();
            $line               = $innerException->getLine();

            error_log("CBEncrypedResponse Inner {$class}:  {$file}({$line}) {$message}");
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
            throw new RuntimeException('The response has already been sent or cancelled.');
        }



        $initializationVector = openssl_random_pseudo_bytes(self::CountOfInitializationVectorBytes);

        $this->response->encryptedJSONBase64        = $this->encryptedJSONBase64($initializationVector);
        $this->response->initializationVectorBase64 = base64_encode($initializationVector);

        header('Content-type: application/json');

        echo json_encode($this->response);

        $this->cancel();
    }
}

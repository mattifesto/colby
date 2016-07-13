<?php

final class CBModelVersionMismatchException extends Exception {

    /**
     * @return CBModelVersionMismatchExceptionß
     */
    public function __construct($message = 'This model has been saved by another session since you started editing it, saving your most recent changes would overwrite the changes made in that session. Reloading your editing page will fetch the changes made by the other session and allow you to save again. If someone else is currently editing this model coordinate your editing with them.', $code = 0, Throwable $previous = null) {
        return parent::__construct($message, $code, $previous);
    }
}

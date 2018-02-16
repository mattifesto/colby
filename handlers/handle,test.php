<?php

$response = new CBAjaxResponse();

if (!ColbyUser::current()->isOneOfThe('Developers')) {
    $response->message  = "You do not have permission to run tests.";
} else {
    $className = $_GET['class'] . 'Tests';
    $functionName = isset($_GET['function']) ? $_GET['function'] . 'Test' : 'test';

    if (is_callable($function = "{$className}::{$functionName}")) {
        try {
            $result = call_user_func($function);
            $response->value = (object)[
                'succeeded' => empty($result->failed),
                'message' => CBModel::valueToString($result, 'message'),
            ];
        } catch (Throwable $throwable) {
            $message = CBMessageMarkup::stringToMarkup(
                CBConvert::throwableToMessage($throwable)
            );

            $stack = CBMessageMarkup::stringToMarkup(
                CBConvert::throwableToStackTrace($throwable)
            );

            $message = <<<EOT

                An exception was thrown while executing the {$function}().

                $message

                --- pre\n{$stack}
                ---

EOT;
            $response->value = (object)[
                'succeeded' => false,
                'message' => $message,
            ];
        }
    } else {
        throw new Exception("The function {$function}() is not callable.");
    }

    $response->wasSuccessful = true;
}

$response->send();

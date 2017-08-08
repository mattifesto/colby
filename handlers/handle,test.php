<?php

$response = new CBAjaxResponse();

if (!ColbyUser::current()->isOneOfThe('Developers')) {
    $response->message  = "You do not have permission to run tests.";
} else {
    $className = $_GET['class'] . 'Tests';
    $functionName = isset($_GET['function']) ? $_GET['function'] . 'Test' : 'test';

    if (is_callable($function = "{$className}::{$functionName}")) {
        $response->message = call_user_func($function);
    } else {
        throw new Exception("The function {$function}() is not callable.");
    }

    $response->wasSuccessful = true;
}

$response->send();

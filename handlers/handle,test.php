<?php

$response = new CBAjaxResponse();

if (!ColbyUser::current()->isOneOfThe('Testers')) {
    $response->message  = "You do not have permission to run tests.";
} else {
    $class                      = $_GET['class'];
    $function                   = isset($_GET['function']) ? $_GET['function'] . 'Test' : 'test';
    $response->message          = call_user_func("{$class}::{$function}");
    $response->wasSuccessful    = true;
}

$response->send();

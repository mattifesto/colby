<?php

$class      = isset($_GET['class']) ? $_GET['class'] : null;
$className  = isset($_GET['className']) ? $_GET['className'] : null; // deprecated
$function   = isset($_GET['function']) ? $_GET['function'] : null;

$args       = isset($_POST['args']) ? json_decode($_POST['args']) : new stdClass();

if ($function) {

    if ($class) {
        $function = "{$class}::{$function}";
    }

    $permissionsFunction = "{$function}Permissions";

    if (is_callable($function) && is_callable($permissionsFunction)) {

        $permissions = call_user_func($permissionsFunction);

        if (ColbyUser::current()->isOneOfThe($permissions->group)) {
            call_user_func($function, $args);
        } else {
            $response           = new CBAjaxResponse();
            $response->message  = "You do not have permission to call `$function`.";
            $response->send();
        }

    } else {

        $response           = new CBAjaxResponse();
        $response->message  = "`$function` is not an Ajax function.";
        $response->send();
    }

} else if ($className) {

    /* Deprecated */

    if (class_exists($className) && is_subclass_of($className, 'CBAPI')) {
        $className::call();
    } else {
        $response           = new CBAjaxResponse();
        $response->message  = "The '{$className}' API was not found.";
        $response->send();
    }
}

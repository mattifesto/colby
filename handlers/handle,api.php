<?php

/**
 * @deprecated 2019_07_05
 *
 *      Ajax functions should be created and called using the CBAjax interfaces
 *      in PHP and Colby.callAjaxFuncion() in JavaScript.
 *
 * This handler makes calling Ajax functions easier and it provides a simple
 * standard for developing Ajax functions.
 *
 * @param string class
 *
 *     Required. However while the deprecated `className` parameter is still
 *     supported we can't technically require it or it would break old code. But
 *     for new code written, treat it as required.
 *
 * @param string function
 *
 *     Optional. The base name of the function to be called on the class. The
 *     suffix "ForAjax" will be appended to this value if it isn't already
 *     there. So if "go" is the value, `goForAjax` will be the actual function
 *     that will be called on the specified class. If no value is specified, the
 *     default value is "execute" and the `executeForAjax` method will be called
 *     on the specified class. Sometimes a class is built to support a single
 *     Ajax function and when that happens the class name also often describes
 *     the function. In those cases, there's no reason to force a specific
 *     function to be specified also.
 *
 * @param string args
 *
 *     Optional. This is a JSON string that will be decoded and the resulting
 *     value will be used as an argument to the specified function.
 *
 * @return string (JSON)
 *
 *     The recommended behavior is that he called function create a
 *     CBAjaxResponse object and set whatever values on it that are required by
 *     the caller.
 *
 *     The called function takes over the communication with the client. Because
 *     of that, the contract for a response to a successful call to the function
 *     is between the function and the caller, and the function is free to
 *     return data formatted in any way, such as XML. The function should be
 *     documented with its own behavior.
 *
 *     If there is an error such that the function can't be called, a
 *     CBAjaxResponse object will be created to return JSON containing error
 *     information.
 */

try {

    $class =
    isset($_GET['class']) ?
    $_GET['class'] :
    null;

    $function =
    isset($_GET['function']) ?
    $_GET['function'] :
    'execute';

    $args =
    isset($_POST['args']) ?
    json_decode($_POST['args']) :
    (object)[];

    /**
     * @deprecated
     */
    $className =
    isset($_GET['className']) ?
    $_GET['className'] :
    null;

    if ($className) { /* deprecated */
        if (
            class_exists($className) &&
            is_subclass_of($className, 'CBAPI')
        ) {
            $className::call();
        } else {
            $response = new CBAjaxResponse();
            $response->message = "The '{$className}' API was not found.";

            $response->send();
        }
    } else {
        if (!preg_match('/ForAjax$/', $function)) {
            $function = "{$function}ForAjax";
        }

        if ($class) {
            $function = "{$class}::{$function}";
        }

        $permissionsFunction = "{$function}Permissions";

        if (is_callable($function) && is_callable($permissionsFunction)) {
            $permissions = call_user_func($permissionsFunction);

            if (
                'Public' === $permissions->group ||
                ColbyUser::current()->isOneOfThe($permissions->group)
            ) {
                call_user_func($function, $args);
            } else {
                $response = new CBAjaxResponse();

                if (ColbyUser::current()->isLoggedIn()) {
                    $response->message =
                    "You do not have permission to call `$function`.";

                    $response->userMustLogIn = false;
                } else {
                    $response->message =
                    "The operation you requested cannot be performed " .
                    "because you are not currently logged in, possibly " .
                    "because your session has timed out. Reloading the " .
                    "current page will usually remedy this.";

                    $response->userMustLogIn = true;
                }

                $response->send();
            }
        } else {
            $response = new CBAjaxResponse();
            $response->message = "`$function` is not an Ajax function.";

            $response->send();
        }
    }

} catch (Throwable $throwable) {

    $respose = new CBAjaxResponse();
    throw $throwable;

}

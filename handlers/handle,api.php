<?php

if (isset($_GET['className'])) {

    $className = $_GET['className'];

    if (class_exists($className) &&
        is_subclass_of($className, 'CBAPI')) {

        $className::call();

    } else {

        $response           = new CBAjaxResponse();
        $response->message  = "The '{$className}' API was not found.";
        $response->send();
    }
}

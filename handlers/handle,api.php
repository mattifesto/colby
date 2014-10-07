<?php

if (isset($_GET['className'])) {

    $className = $_GET['className'];

    if (is_subclass_of($className, 'CBAPI')) {

        $className::call();
    }
}

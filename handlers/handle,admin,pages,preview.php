<?php

if (!ColbyUser::current()->isOneOfThe('Administrators')) {
    return include CBSystemDirectory . '/handlers/handle-authorization-failed.php';
}

$ID = $_GET['ID'];
$version = empty($_GET['version']) ? null : $_GET['version'];

if (empty($version)) {
    $model = CBModels::fetchModelByID($ID);
} else {
    $model = CBModels::fetchModelByIDWithVersion($ID, $version);
}


if (!empty($model->className) &&
    is_callable($function = "{$model->className}::renderModelAsHTML"))
{
    call_user_func($function, $model);
} else {
    echo 'No page exists for the provided data store ID and version.';
}

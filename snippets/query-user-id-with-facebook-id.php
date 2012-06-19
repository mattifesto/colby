<?php

// variable $facebookId should be set before including this snippet

$mysqli = Colby::mysqli();

$facebookId = $mysqli->escape_string($facebookId);

$sql = "SELECT GetUserIdWithFacebookId('{$facebookId}') as `id`";

$result = $mysqli->query($sql);

if ($mysqli->error)
{
    throw new RuntimeException($mysqli->error);
}

$userId = $result->fetch_object()->id;

$result->free();

return $userId;

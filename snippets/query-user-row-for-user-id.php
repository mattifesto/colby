<?php

// variable $userId should be set before including this snippet

$mysqli = Colby::mysqli();

$safeUserId = $mysqli->escape_string($userId);
$safeUserId = "'{$safeUserId}'";

$sql = <<< END
SELECT
    *
FROM
    `ColbyUsers`
WHERE
    `id` = {$userId}
END;

$result = $mysqli->query($sql);

if ($mysqli->error)
{
    throw new RuntimeException($mysqli->error);
}

if (0 === $result->num_rows)
{
    $userRow = null;
}
else
{
    $userRow = $result->fetch_object();
}

$result->free();

return $userRow;

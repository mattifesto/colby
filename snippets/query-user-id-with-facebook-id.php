<?php

// variable $facebookId should be set before including this snippet

$mysqli = Colby::mysqli();

$safeFacebookId = $mysqli->escape_string($facebookId);
$safeFacebookId = "'{$safeFacebookId}'";

$sql = <<< END
SELECT
    `id`
FROM
    `ColbyUsers`
WHERE
    `facebookId` = {$safeFacebookId}
END;

$result = $mysqli->query($sql);

if ($mysqli->error)
{
    throw new RuntimeException($mysqli->error);
}

if (0 === $result->num_rows)
{
    $userId = null;
}
else
{
    $userId = $result->fetch_object()->id;
}

$result->free();

return $userId;

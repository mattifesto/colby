<?php

// variable $sequenceName should be set before including this snippet

$mysqli = Colby::mysqli();

$sequenceName = $mysqli->escape_string($sequenceName);

$sql = "SELECT GetNextInsertIdForSequence('{$sequenceName}') AS `id`";

$result = $mysqli->query($sql);

if ($mysqli->error)
{
    throw new RuntimeException($mysqli->error);
}

$nextSequenceId = $result->fetch_object()->id;

$result->free();

return $nextSequenceId;

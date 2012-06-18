<?php

// variable $sequenceName should be set before including this snippet

$mysqli = Colby::mysqli();

$safeSequenceName = $mysqli->escape_string($sequenceName);
$safeSequenceName = "'{$safeSequenceName}'";

$sql = <<< END
SELECT
    GetNextInsertIdForSequence({$safeSequenceName})
AS
    `id`;
END;

$result = $mysqli->query($sql);

if ($mysqli->error)
{
    throw new RuntimeException($mysqli->error);
}

$nextSequenceId = $result->fetch_object()->id;

$result->free();

return $nextSequenceId;

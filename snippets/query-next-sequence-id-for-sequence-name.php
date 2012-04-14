<?php

// variable $sequenceName should be set before including this snippet

$mysqli = Colby::mysqli();

$safeSequenceName = $mysqli->escape_string($sequenceName);
$safeSequenceName = "'{$safeSequenceName}'";

$sql = <<< END
UPDATE
    `ColbySequences`
SET
    `id` = LAST_INSERT_ID(`id` + 1)
WHERE
    `name` = {$safeSequenceName}
END;

$mysqli->query($sql);

if ($mysqli->error)
{
    throw new RuntimeException($mysqli->error);
}

$sql = <<< END
SELECT LAST_INSERT_ID() as `id`;
END;

$result = $mysqli->query($sql);

if ($mysqli->error)
{
    throw new RuntimeException($mysqli->error);
}

$nextSequenceId = $result->fetch_object()->id;

$result->free();

return $nextSequenceId;

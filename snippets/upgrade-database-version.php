<?php

$sql = <<<EOT
DROP FUNCTION ColbySchemaVersionNumber
EOT;

Colby::query($sql);

/**
 * Heredocs won't parse constants so the version number must be placed
 * in a variable.
 */
$versionNumber = COLBY_VERSION_NUMBER;

/**
 * ColbySchemaVersionNumber
 */
$sql = <<<EOT
CREATE FUNCTION ColbySchemaVersionNumber()
RETURNS BIGINT UNSIGNED
BEGIN
    RETURN {$versionNumber};
END
EOT;

Colby::query($sql);

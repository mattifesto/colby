<?php

header('Content-type: application/json');

$response = new ColbyOutputManager('ajax-response');

$response->begin();

if (!ColbyUser::current()->isOneOfThe('Developers'))
{
    $response->message = 'You are not authorized to use this feature.';

    goto done;
}

$userId = (int)$_POST['userId'];
$groupName = $_POST['groupName'];
$shouldBeInGroup = !!$_POST['shouldBeInGroup'];

$tableName = "ColbyUsersWhoAre{$groupName}";

if ($shouldBeInGroup)
{
    $sql = <<<EOT
INSERT INTO
    `{$tableName}`
VALUES
(
    {$userId},
    NOW()
)
ON DUPLICATE KEY UPDATE
    `added` = `added`
EOT;

    Colby::query($sql);
}
else
{
    $sql = <<<EOT
DELETE FROM
    `{$tableName}`
WHERE
    `userId` = {$userId}
EOT;

    Colby::query($sql);
}

/**
 * Send the response
 */

$response->wasSuccessful = true;
$response->message = "The site was successfully updated.";

done:

$response->end();

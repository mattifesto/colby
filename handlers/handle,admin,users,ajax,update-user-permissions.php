<?php

if (!ColbyUser::current()->isOneOfThe('Developers'))
{
    return include CBSystemDirectory . '/handlers/handle-authorization-failed-ajax.php';
}

$response = new CBAjaxResponse();

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

$response->send();

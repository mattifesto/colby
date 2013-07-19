<?php

header('Content-type: application/json');

$response = new stdClass();
$response->wasSuccessful = false;
$response->message = 'The request to alter the user\'s verification status was not successful.';

try
{
    // SECURITY: Only verified users can alter the verification status
    //           of other users.

    $userRow = ColbyUser::userRow();

    if (!$userRow || !$userRow->hasBeenVerified)
    {
        goto done;
    }

    $mysqli = Colby::mysqli();

    $userId = $mysqli->escape_string($_POST['id']);
    $hasBeenVerified = $mysqli->escape_string($_POST['hasBeenVerified']);

    $sql = "UPDATE `ColbyUsers` SET `hasBeenVerified` = b'{$hasBeenVerified}' WHERE `id` = '{$userId}'";

    Colby::query($sql);

    $response->wasSuccessful = true;
    $response->message = 'The request to alter the user\'s verification status was successful.';
}
catch (Exception $e)
{
    $response->message = 'An exception was thrown while trying to update the user\'s verification status: "' . $e->getMessage() . '"';
}

done:

echo json_encode($response);

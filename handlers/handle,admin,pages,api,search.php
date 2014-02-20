<?php

include_once CBSystemDirectory . '/classes/CBDataStore.php';
include_once CBSystemDirectory . '/classes/CBPages.php';


$response = new ColbyOutputManager('ajax-response');

$response->begin();

if (!ColbyUser::current()->isOneOfThe('Administrators'))
{
    $response->message = 'You are not authorized to use this feature.';

    goto done;
}

/**
 *
 */

$queryText = $_POST['queryText'];


/**
 *
 */

$result     = CBSearchForPages($queryText);
$matches    = array();

if ($result)
{
    while ($row = $result->fetch_object())
    {
        $matches[] = $row;
    }

    $result->free();
}

$response->matches = $matches;

/**
 * Send the response
 */

$response->wasSuccessful = true;

done:

$response->end();


/**
 *
 */
function CBSearchForPages($queryText)
{
    $words          = preg_split('/[\s,]+/', $queryText, null, PREG_SPLIT_NO_EMPTY);
    $likesForSQL    = array();

    foreach ($words as $word)
    {
        if (strlen($word) > 2)
        {
            $wordForSQL = ColbyConvert::textToSQl($word);

            $likesForSQL[] = "`searchText` LIKE '%{$wordForSQL}%'";
        }
    }

    if (empty($likesForSQL))
    {
        return false;
    }

    $likesForSQL = implode(' AND ', $likesForSQL);

    $sql = <<<EOT

        SELECT
            LOWER(HEX(`archiveID`)) AS `dataStoreID`,
            `titleHTML`,
            `published`
        FROM
            `ColbyPages`
        WHERE
            {$likesForSQL}
        ORDER BY
            `published` IS NULL DESC,
            `published` DESC

EOT;

    return Colby::query($sql);
}

<?php

if (!ColbyUser::current()->isOneOfThe('Administrators'))
{
    return include CBSystemDirectory . '/handlers/handle-authorization-failed-ajax.php';
}

$response = new CBAjaxResponse();

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

$response->send();


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

    $likesForSQL    = implode(' AND ', $likesForSQL);
    $CBPageTypeID   = CBPageTypeID;

    $sql = <<<EOT

        SELECT
            LOWER(HEX(`archiveID`)) AS `dataStoreID`,
            `titleHTML`,
            `published`
        FROM
            `ColbyPages`
        WHERE
            `typeID` = UNHEX('{$CBPageTypeID}') AND
            {$likesForSQL}
        ORDER BY
            `published` IS NULL DESC,
            `published` DESC

EOT;

    return Colby::query($sql);
}

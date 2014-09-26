<?php

if (!ColbyUser::current()->isOneOfThe('Administrators'))
{
    return include CBSystemDirectory . '/handlers/handle-authorization-failed-ajax.php';
}

$response           = new CBAjaxResponse();
$response->pages    = CBGetRecentlyEditedPages::pages();


/**
 * Send the response
 */

$response->wasSuccessful = true;

$response->send();


/**
 *
 */
class CBGetRecentlyEditedPages {

    public static function pages() {

        $pages  = array();
        $SQL    = <<<EOT

            SELECT
                `page`.`keyValueData`
            FROM
                `CBPageLists` AS `list`
            LEFT JOIN
                `ColbyPages` AS `page`
            ON
                `page`.`ID` = `list`.`pageRowID`
            WHERE
                `list`.`listClassName`  = 'CBRecentlyEditedPages'
            ORDER BY
                `sort1` DESC

EOT;

        $result = Colby::query($SQL);

        while ($row = $result->fetch_object())
        {
            $pages[] = json_decode($row->keyValueData);
        }

        $result->free();

        return $pages;
    }
}

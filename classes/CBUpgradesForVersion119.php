<?php

/**
 * 2015.02.11
 */
class CBUpgradesForVersion119 {

    /**
     * @return void
     */
    public static function run() {
        $typeID = ColbyConvert::textToSQL(CBPageTypeID);
        $typeID = "UNHEX('{$typeID}')";
        $SQL    = <<<EOT

            UPDATE
                `ColbyPages`
            SET
                `className` = 'CBViewPage',
                `typeID`    = NULL
            WHERE
                `typeID`    = {$typeID}
EOT;

        Colby::query($SQL);
    }
}

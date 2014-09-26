<?php

class CBDictionaryTuple
{
    private $key;

    public $number  = 1;
    public $value   = null;

    /**
     * @return CBDictionaryTuple
     */
    private function __construct()
    {
    }

    /**
     * This function will get the data in the `CBDictionary` table associated
     * with a key and return a class instance for it. If the key does not exist
     * in the table, a new row will be created for it.
     *
     * @return CBDictionaryTuple
     */
    public static function initWithKey($key)
    {
        $tuple      = new CBDictionaryTuple();
        $tuple->key = (string)$key;
        $row        = $tuple->getRow();

        if ($row) {

            $tuple->number  = (int)$row->number;
            $tuple->value   = json_decode($row->valueJSON);

        } else {

            $tuple->insertRow();
        }

        return $tuple;
    }

    /**
     * @return void
     */
    public static function deleteForKey($key)
    {
        $keyForSQL  = ColbyConvert::textToSQL($key);
        $SQL        = <<<EOT

            DELETE FROM
                `CBDictionary`
            WHERE
                `key` = '{$keyForSQL}'

EOT;

        Colby::query($SQL);
    }

    /**
     * @return bool
     */
    public static function existsForKey($key)
    {
        // TODO: Implement
    }

    /**
     * @return object
     */
    private function getRow()
    {
        $keyForSQL  = ColbyConvert::textToSQL($this->key);
        $SQL        = <<<EOT

            SELECT
                `valueJSON`,
                `number`
            FROM
                `CBDictionary`
            WHERE
                `key` = '{$keyForSQL}'

EOT;

        $result = Colby::query($SQL);

        if (0 == $result->num_rows)
        {
            $row = null;
        }
        else
        {
            $row = $result->fetch_object();
        }

        $result->free();

        return $row;
    }

    /**
     * @return object
     */
    private function insertRow()
    {
        $keyForSQL          = ColbyConvert::textToSQL($this->key);
        $numberForSQL       = (int)$this->number;
        $valueJSONForSQL    = json_encode($this->value);
        $SQL                = <<<EOT

            INSERT INTO
                `CBDictionary`
            SET
                `key`       = '{$keyForSQL}',
                `valueJSON` = '{$valueJSONForSQL}',
                `number`    = {$numberForSQL}

EOT;

        Colby::query($SQL);
    }

    /**
     * This function increments the number for a key in the database and returns
     * the new number. This function acts as a sequence generator.
     *
     * @return int
     */
    public static function nextNumberForKey($key)
    {
        // TODO: Implement
    }

    /**
     * This function increments the number for a key in the database and returns
     * the new number. If the number in the database goes beyond $rangeMax
     * the number in the database will be set to $rangeMin and $rangeMin will
     * be returned. This function acts as a repeating sequence generator.
     *
     * @return int
     */
    public static function nextNumberForKeyInRange($key, $rangeMin, $rangeMax)
    {
        // TODO: Implement
    }

    /**
     * @return void
     */
    public function update()
    {
        $keyForSQL          = ColbyConvert::textToSQL($this->key);
        $numberForSQL       = (int)$this->number;
        $valueJSONForSQL    = json_encode($this->value);
        $SQL                = <<<EOT

            UPDATE
                `CBDictionary`
            SET
                `valueJSON` = '{$valueJSONForSQL}',
                `number`    = {$numberForSQL}
            WHERE
                `key`       = '{$keyForSQL}'

EOT;

        Colby::query($SQL);
    }

    /**
     * This function will update the value in the table and increment both the
     * number in the table and the number member variable, but only if the
     * number in the table matches the number member variable.
     *
     * If the number member variable does not match the number in the table it
     * means that the value member variable has become stale.
     *
     * @return bool
     *  If the value was updated, `true` will be returned; otherwise `false`.
     */
    public function updateForNumber()
    {
        // TODO: Implement correctly

        $this->update();

        return true;
    }
}

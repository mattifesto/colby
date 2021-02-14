<?php

final class
CBLogEntry {

    /* -- accessors -- */



    /**
     * @param object $logEntryModel
     *
     * @return CBID|null
     */
    static function
    getSourceCBID(
        stdClass $logEntryModel
    ): ?string {
        return CBModel::valueAsCBID(
            $logEntryModel,
            'sourceID'
        );
    }
    /* getSourceCBID() */



    /**
     * @param object $logEntryModel
     * @param CBID|null $sourceCBID
     *
     * @return void
     */
    static function
    setSourceCBID(
        stdClass $logEntrySpec,
        ?string $sourceCBID
    ): void {
        if ($sourceCBID !== null) {
            $isCBID = CBID::valueIsCBID($sourceCBID);

            if (!$isCBID) {
                throw new CBExceptionWithValue(
                    'The sourceCBID argument is not a valid CBID.',
                    $sourceCBID,
                    '50fa1c6440484b69430ce0dd4dc9b5dd50579040'
                );
            }
        }

        $logEntrySpec->sourceID = $sourceCBID;
    }
    /* setSourceCBID() */

}

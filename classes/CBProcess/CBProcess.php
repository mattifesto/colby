<?php

final class CBProcess {

    private static $processID = null;



    /**
     * @return null
     */
    static function clearID() {
        if (CBProcess::$processID === null) {
            throw new RuntimeException(
                'The process ID is not set.'
            );
        } else {
            CBProcess::$processID = null;
        }
    }
    /* clearID() */



    /**
     * @return hex160
     */
    static function ID() {
        return CBProcess::$processID;
    }
    /* ID() */



    /**
     * @NOTE
     *
     *      After setting a process ID for the first time, which essentially
     *      creates the process, code should immediately call CBLog::log() with
     *      a message describing the process. The first log entry for a process
     *      acts as the representative entry for that process.
     *
     * @param hex160 $processID
     *
     * @return null
     */
    static function setID($processID) {
        if (CBProcess::$processID !== null) {
            throw new RuntimeException(
                'A process ID has already been set.'
            );
        }

        if (!CBID::valueIsCBID($processID)) {
            $processIDAsJSON = json_encode(
                $processID
            );

            throw new InvalidArgumentException(
                'The provided value is not a valid process ID: ' .
                $processIDAsJSON
            );
        }

        CBProcess::$processID = $processID;
    }
    /* setID() */

}

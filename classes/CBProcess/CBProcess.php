<?php

final class CBProcess {

    private static $processID = null;

    /**
     * @return null
     */
    static function clearID() {
        if (CBProcess::$processID === null) {
            throw new RuntimeException('The process ID is not set.');
        } else {
            CBProcess::$processID = null;
        }
    }

    /**
     * @return hex160
     */
    static function ID() {
        return CBProcess::$processID;
    }

    /**
     * @param hex160 $processID
     *
     * @return null
     */
    static function setID($processID) {
        if (CBProcess::$processID !== null) {
            throw new RuntimeException('A process ID has already been set.');
        }

        if (!CBHex160::is($processID)) {
            $processIDAsJSON = json_encode($processID);
            throw new InvalidArgumentException("The provided value is not a valid process ID: {$processIDAsJSON}");
        }

        CBProcess::$processID = $processID;
    }
}

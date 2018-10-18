<?php

/**
 * This class provides shared tools to model import processes. The goal is to
 * move to a single extensible model import process, but until that is
 * accomplished, these tools must be usable by all different import processes.
 */
final class CBModelImporter {

    /**
     * @return [string]
     */
    static function CBHTMLOutput_JavaScriptURLs() {
        return [
            Colby::flexpath(__CLASS__, 'v460.js', cbsysurl()),
        ];
    }

    /**
     * @return [[<name>, <value>]]
     */
    static function CBHTMLOutput_JavaScriptVariables(): array {
        return [
            ['CBModelImporter_processID', CBModelImporter::processID()],
        ];
    }

    /**
     * @return ID
     */
    static function processID(): string {
        return '456606c35357dbd0e6c6b432632d9254ce4c8d54';
    }
}

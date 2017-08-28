<?php

class CBUnitTestsForCBDataStore {

    /**
     * @return void
     */
    static function runAll() {
        $ID             = 'bc1ee25654bfdd854ef2a3d7f363198b71559769';
        $directoryName  = CBDataStore::directoryNameFromDocumentRoot($ID);
        $expected       = 'data/bc/1e/e25654bfdd854ef2a3d7f363198b71559769';

        if ($directoryName != $expected) {
            throw new RuntimeException('A test of "CBDataStore::directoryNameFromDocumentRoot" failed.');
        }
    }
}

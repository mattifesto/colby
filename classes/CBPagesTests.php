<?php

final class CBPagesTests {

    /**
     * @return [<hex160> : {string} | null]
     */
    private static function fetchURIs($IDs) {
        $IDsAsSQL   = CBHex160::toSQL($IDs);
        $SQL        = <<<EOT

            SELECT  LOWER(HEX(`archiveID`)), `URI`
            FROM    `ColbyPages`
            WHERE   `archiveID` IN ({$IDsAsSQL})

EOT;

        return CBDB::SQLToArray($SQL);
    }

    /**
     * @return null
     */
    public static function updateURIsTest() {
        $IDs            = [
            'f88086cae913d6264f171a0d2d2f159d02e332c4',
            '1d43d3822808f99399b2688cfcf57935efe15b33',
            '22eb92cd569ffb0718f25a794456c6073b55f0ec',
            'ce712a8116428172d3dabfa8682b1230241096d8'
        ];

        $URIs           = [
            'updateURIsTest/uri0',
            'updateURIsTest/uri1',
            'updateURIsTest/uri2'
        ];

        Colby::query('START TRANSACTION');

        array_walk($IDs, 'CBPages::insertRow');

        // Test 1 : all preferred URIs should be used

        $preferredURIs = [
            $IDs[0] => $URIs[0],
            $IDs[1] => $URIs[1],
            $IDs[2] => $URIs[2],
        ];

        $returnedURIs = CBPages::updateURIs(['preferredURIs' => $preferredURIs]);

        if ($returnedURIs != $preferredURIs) {
            $r = json_encode($returnedURIs);
            $p = json_encode($preferredURIs);
            throw new exception("The returned URIs: {$r} do not match the preferred URIs: {$p}");
        }

        $actualURIs = self::fetchURIs([$IDs[0], $IDs[1], $IDs[2]]);

        if ($actualURIs != $preferredURIs) {
            $a = json_encode($actualURIs);
            $p = json_encode($preferredURIs);
            throw new exception("The actual URIs: {$a} do not match the preferred URIs: {$p}");
        }

        // Test 2: duplicate URIs passed in

        $preferredURIs = [
            $IDs[0] => $URIs[0],
            $IDs[1] => $URIs[0],
            $IDs[2] => $URIs[0],
        ];

        $expectedURIs = [
            $IDs[0] => $URIs[0],
            $IDs[1] => $IDs[1],
            $IDs[2] => $IDs[2],
        ];

        $returnedURIs = CBPages::updateURIs(['preferredURIs' => $preferredURIs]);

        if ($returnedURIs != $expectedURIs) {
            $r = json_encode($returnedURIs);
            $e = json_encode($expectedURIs);
            throw new exception("The returned URIs: {$r} do not match the expected URIs: {$e}");
        }

        $actualURIs = self::fetchURIs([$IDs[0], $IDs[1], $IDs[2]]);

        if ($actualURIs != $expectedURIs) {
            $a = json_encode($actualURIs);
            $e = json_encode($expectedURIs);
            throw new exception("The actual URIs: {$a} do not match the expected URIs: {$e}");
        }

        // Test 3: null and duplicate URI in database

        $preferredURIs = [
            $IDs[1] => null,
            $IDs[2] => null,
            $IDs[3] => $URIs[0]
        ];

        $expectedURIs = [
            $IDs[1] => null,
            $IDs[2] => null,
            $IDs[3] => $IDs[3]
        ];

        $returnedURIs = CBPages::updateURIs(['preferredURIs' => $preferredURIs]);

        if ($returnedURIs != $expectedURIs) {
            $r = json_encode($returnedURIs);
            $e = json_encode($expectedURIs);
            throw new exception("The returned URIs: {$r} do not match the expected URIs: {$e}");
        }

        $actualURIs = self::fetchURIs([$IDs[1], $IDs[2], $IDs[3]]);

        if ($actualURIs != $expectedURIs) {
            $a = json_encode($actualURIs);
            $e = json_encode($expectedURIs);
            throw new exception("The actual URIs: {$a} do not match the expected URIs: {$e}");
        }

        Colby::query('ROLLBACK');
    }
}

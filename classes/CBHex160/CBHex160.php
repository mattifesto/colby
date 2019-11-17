<?php

/**
 * @deprecated 2019_11_15
 *
 *      This class has been replaced by the CBID class.
 */
final class CBHex160 {

    /* -- functions -- -- -- -- -- */



    /**
     * @deprecated use CBID::valueIsCBID()
     */
    static function is($value): bool {
        return CBID::valueIsCBID($value);
    }



    /**
     * @deprecated use CBID::generateRandomCBID()
     */
    static function random(): string {
        return CBID::generateRandomCBID();
    }



    /**
     * @deprecated use CBID::toSQL()
     */
    static function toSQL($values) {
        return CBID::toSQL($values);
    }

}

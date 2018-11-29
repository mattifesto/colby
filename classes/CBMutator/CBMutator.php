<?php

/**
 * A mutator is an object that represents a value. The value can be retreived
 * and modified. Most importantly listeners can receive notifications when the
 * value changes.
 *
 * CBUIRadioButton instances change the value of a CBMutator.
 */
final class CBMutator {

    /**
     * @return [string]
     */
    static function CBHTMLOutput_JavaScriptURLs(): array {
        return [
            Colby::flexpath(__CLASS__, 'v468.js', cbsysurl()),
        ];
    }
}

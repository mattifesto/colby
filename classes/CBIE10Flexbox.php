<?php

/**
 * This class contains functions that help support flexbox on the IE10 browser.
 */
final class CBIE10Flexbox {

    /**
     * Traslates an `align-items` value to an `-ms-flex-align` value.
     *
     * @return {string}
     */
    public static function alignItemsToFlexAlign($alignItems) {
        $translation = [
            'flex-start'    => 'start',
            'flex-end'      => 'end',
            'center'        => 'center',
            'stretch'       => 'stretch',
            'baseline'      => 'baseline'
        ];

        return $translation[$alignItems];
    }

    /**
     * Translates an `align-self` value to an `-ms-flex-item-align` value.
     *
     * @return {string}
     */
    public static function alignSelfToFlexItemAlign($alignSelf) {
        $translation = [
            'auto'          => 'auto',
            'flex-start'    => 'start',
            'flex-end'      => 'end',
            'center'        => 'center',
            'stretch'       => 'stretch',
            'baseline'      => 'baseline'
        ];

        return $translation[$alignSelf];
    }

    /**
     * Translates a `justify-content` value to a `-ms-flex-pack` value.
     *
     * @return {string}
     */
    public static function justifyContentToFlexPack($justifyContent) {
        $translation = [
            'flex-start'    => 'start',
            'flex-end'      => 'end',
            'center'        => 'center',
            'space-around'  => 'justify',
            'space-between' => 'justify'
        ];

        return $translation[$justifyContent];
    }
}

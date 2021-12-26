<?php

final class
CB_ModelAssociation {

    /* -- accessors -- */



    /**
     * @param object $modelAssociation
     *
     * @return string
     */
    static function
    getAssociationKey(
        stdClass $modelAssociation
    ): string {
        return CBModel::valueToString(
            $modelAssociation,
            'CB_ModelAssociation_associationKey'
        );
    }
    /* getAssociationKey() */



    /**
     * @param object $modelAssociation
     * @param string $associationKey
     *
     * @return void
     */
    static function
    setAssociationKey(
        stdClass $modelAssociation,
        string $associationKey
    ): void {
        $modelAssociation->CB_ModelAssociation_associationKey = $associationKey;
    }
    /* setAssociationKey() */



    /**
     * @param object $modelAssociation
     *
     * @return string|null
     */
    static function
    getFirstCBID(
        stdClass $modelAssociation
    ): ?string {
        return CBModel::valueAsCBID(
            $modelAssociation,
            'CB_ModelAssociation_firstCBID'
        );
    }
    /* getFirstCBID() */



    /**
     * @param object $modelAssociation
     * @param CBID|null $firstCBID
     *
     * @return void
     */
    static function
    setFirstCBID(
        stdClass $modelAssociation,
        ?string $firstCBID
    ): void {
        $modelAssociation->CB_ModelAssociation_firstCBID = $firstCBID;
    }
    /* setFirstCBID() */



    /**
     * @param object $modelAssociation
     *
     * @return string|null
     */
    static function
    getSecondCBID(
        stdClass $modelAssociation
    ): ?string {
        return CBModel::valueAsCBID(
            $modelAssociation,
            'CB_ModelAssociation_secondCBID'
        );
    }
    /* getSecondCBID() */



    /**
     * @param object $modelAssociation
     * @param CBID|null $secondCBID
     *
     * @return void
     */
    static function
    setSecondCBID(
        stdClass $modelAssociation,
        ?string $secondCBID
    ): void {
        $modelAssociation->CB_ModelAssociation_secondCBID = $secondCBID;
    }
    /* setSecondCBID() */



    /**
     * @param object $modelAssociation
     *
     * @return int
     */
    static function
    getSortingValue(
        stdClass $modelAssociation
    ): int {
        return CBModel::valueAsInt(
            $modelAssociation,
            'CB_ModelAssociation_sortingValue'
        ) ?? 0;
    }
    /* getSortingValue() */



    /**
     * @param object $modelAssociation
     * @param int $sortingValue
     *
     * @return void
     */
    static function
    setSortingValue(
        stdClass $modelAssociation,
        int $sortingValue
    ): void {
        $modelAssociation->CB_ModelAssociation_sortingValue = $sortingValue;
    }
    /* setSortingValue() */



    /**
     * @param object $modelAssociation
     *
     * @return int
     */
    static function
    getSortingValueDifferentiator(
        stdClass $modelAssociation
    ): int {
        return CBModel::valueAsInt(
            $modelAssociation,
            'CB_ModelAssociation_sortingValueDifferentiator'
        ) ?? 0;
    }
    /* getSortingValueDifferentiator() */



    /**
     * This function exists to set the sorting differentiator of an association
     * fetched from the database. The sorting differentiator can't and won't
     * ever be saved to the database.
     *
     * @param object $modelAssociation
     * @param int $sortingValueDifferentiator
     *
     * @return void
     */
    static function
    setSortingValueDifferentiator(
        stdClass $modelAssociation,
        int $sortingValueDifferentiator
    ): void {
        $modelAssociation->CB_ModelAssociation_sortingValueDifferentiator = (
            $sortingValueDifferentiator
        );
    }
    /* setSortingValueDifferentiator() */

}

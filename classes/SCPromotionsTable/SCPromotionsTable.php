<?php

final class SCPromotionsTable {

    /**
     * This static variable is set by fetchCachedActivePromotionModels().
     */
    private static $cachedActivePromotionModels = null;



    /* -- CBAjax interfaces -- -- -- -- -- */



    /**
     * @return [object]
     */
    static function CBAjax_fetchSummaries(): array {
        $promotionModels = CBModels::fetchModelsByClassName2(
            'SCPromotion'
        );

        $promotionSummaries = array_map(
            function ($promotionModel) {
                return (object)[
                    'ID' => $promotionModel->ID,

                    'title' => CBModel::valueToString(
                        $promotionModel,
                        'title'
                    ),

                    'beginTimestamp' => CBModel::valueAsInt(
                        $promotionModel,
                        'beginTimestamp'
                    ),

                    'endTimestamp' => CBModel::valueAsInt(
                        $promotionModel,
                        'endTimestamp'
                    ),
                ];
            },
            $promotionModels
        );

        return $promotionSummaries;
    }
    /* CBAjax_fetchSpecs() */



    /**
     * @return string
     */
    static function CBAjax_fetchSummaries_getUserGroupClassName(): string {
        return 'CBAdministratorsUserGroup';
    }



    /* -- CBInstall interfaces -- -- -- -- -- */



    /**
     * @NOTE Indexes
     *
     *      Eventually, all of the beginTimestamp values is this table will be
     *      before the current timestamp. The endTimestamp index is used to
     *      quickly find promotions that have not ended yet, which will usually
     *      be a small minority of the total promotions.
     */
    static function CBInstall_install(): void {
        $SQL = <<<EOT

            CREATE TABLE IF NOT EXISTS SCPromotions (
                CBID            BINARY(20) NOT NULL,
                beginTimestamp  BIGINT NOT NULL,
                endTimestamp    BIGINT NOT NULL,

                PRIMARY KEY         (CBID),
                KEY endTimestamp    (endTimestamp)
            )
            ENGINE=InnoDB
            DEFAULT CHARSET=utf8mb4
            COLLATE=utf8mb4_unicode_520_ci

        EOT;

        Colby::query($SQL);
    }
    /* CBInstall_install() */



    /* -- functions -- -- -- -- -- */



    /**
     * @param CBID $CBID
     *
     * @return void
     */
    static function deletePromotionByCBID(
        string $CBID
    ): void {
        $CBIDAsSQL = CBID::toSQL($CBID);

        $SQL = <<<EOT

            DELETE FROM SCPromotions
            WHERE CBID IN ({$CBIDAsSQL})

        EOT;

        Colby::query($SQL);
    }
    /* deletePromotionByCBID() */



    /**
     * @return [object]
     */
    static function fetchActivePromotionCBIDs(
        ?int $currentTimestamp = null
    ): array {
        if ($currentTimestamp === null) {
            $currentTimestamp = time();
        }

        $SQL = <<<EOT

            SELECT  LOWER(HEX(CBID)) AS CBID

            FROM    SCPromotions

            WHERE   endTimestamp > {$currentTimestamp} &&
                    beginTimestamp <= {$currentTimestamp}

        EOT;

        return CBDB::SQLToArrayOfNullableStrings($SQL);
    }
    /* fetchActivePromotionCBIDs() */



    /**
     * The array of promotion models is cached the first time this function is
     * called. There is not currently a function to clear the cache. If a need
     * exists for such a function add it to this class.
     *
     * @return [object]
     */
    static function fetchCachedActivePromotionModels(): array {
        $cachedActivePromotionModels = (
            SCPromotionsTable::$cachedActivePromotionModels
        );

        if ($cachedActivePromotionModels === null) {
            $activePromotionCBIDs = (
                SCPromotionsTable::fetchActivePromotionCBIDs()
            );

            $cachedActivePromotionModels = CBModels::fetchModelsByID2(
                $activePromotionCBIDs
            );

            SCPromotionsTable::$cachedActivePromotionModels = (
                $cachedActivePromotionModels
            );
        }

        return CBModel::clone(
            $cachedActivePromotionModels
        );
    }
    /* fetchCachedActivePromotionModels() */



    /**
     * @param object $promotionModel
     *
     * @return void
     */
    static function insertPromotion(
        stdClass $promotionModel
    ): void {
        $promotionCBID = CBModel::valueAsCBID(
            $promotionModel,
            'ID'
        );

        $beginTimestamp = CBModel::valueAsInt(
            $promotionModel,
            'beginTimestamp'
        );


        $endTimestamp = CBModel::valueAsInt(
            $promotionModel,
            'endTimestamp'
        );

        if (
            $promotionCBID === null ||
            $beginTimestamp === null ||
            $endTimestamp === null
        ) {
            throw new CBExceptionWithValue(
                CBConvert::stringToCleanLine(<<<EOT

                    One or more of the following properties is invalid on this
                    model: "ID", "beginTimestamp", and "endTimestamp".

                EOT),
                $promotionModel,
                'bca0bfb15949394096458d098167e7c2fc2295b9'
            );
        }

        $promotionCBIDAsSQL = CBID::toSQL($promotionCBID);

        $SQL = <<<EOT

            INSERT INTO SCPromotions
            (
                CBID,
                beginTimestamp,
                endTimestamp
            )
            VALUES (
                {$promotionCBIDAsSQL},
                {$beginTimestamp},
                {$endTimestamp}
            )
            ON DUPLICATE KEY
            UPDATE  beginTimestamp = {$beginTimestamp},
                    endTimestamp = {$endTimestamp}

        EOT;

        Colby::query($SQL);
    }
    /* insertPromotion() */

}

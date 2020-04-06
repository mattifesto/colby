<?php

final class SCOrdersTable {

    /* -- CBInstall interfaces -- -- -- -- -- */



    /**
     * Indexes:
     *
     *      isArchived_createdYearMonth_created
     *
     *      This index is used for two purposes. 1) Create a list of
     *      createdYearMonth and COUNT(*) to provide an index of archived
     *      orders by month. 2) Create a sorted list of archived orders for a
     *      given createdYearMonth as the target for a link from (1).
     *
     *      isArchived_isAuthorized_created
     *
     *      This index is used to create list of new orders where the orders 1)
     *      are not archived 2) are authorized and 3) are sorted by their
     *      creation date.
     *
     *      isAuthorized_created
     *
     *      This index is used to create a list of unauthorized orders older
     *      than 24-hours to be deleted. These are orders that are created but
     *      the customer either never entered their credit card information or
     *      their credit card information was rejected.
     *
     * @return void
     */
    static function CBInstall_install(): void {
        $SQL = <<<EOT

            CREATE TABLE IF NOT EXISTS SCOrders
            (
                id                  BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
                archiveId           BINARY(20) NOT NULL,
                created             BIGINT NOT NULL,
                createdYearMonth    CHAR(6) NOT NULL,
                isArchived          BIT(1) NOT NULL DEFAULT b'0',
                isAuthorized        BIT(1) NOT NULL DEFAULT b'0',

                PRIMARY KEY (id),

                UNIQUE KEY archiveId (archiveId),

                KEY isArchived_createdYearMonth_created
                    (isArchived, createdYearMonth, created),

                KEY isArchived_isAuthorized_created
                    (isArchived, isAuthorized, created),

                KEY isAuthorized_created
                    (isAuthorized, created)
            )
            ENGINE=InnoDB
            DEFAULT CHARSET=utf8mb4
            COLLATE=utf8mb4_unicode_520_ci

        EOT;

        Colby::query($SQL);
    }
    /* CBInstall_install() */

}

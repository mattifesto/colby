<?php

final class
CB_CBAdmin_NewOrders {

    /* -- CBAdmin interfaces -- -- -- -- -- */



    /**
     * @return string
     */
    static function
    CBAdmin_getUserGroupClassName(
    ): string {
        return 'CBAdministratorsUserGroup';
    }
    /* CBAdmin_getUserGroupClassName() */



    /**
     * @return [string]
     */
    static function
    CBAdmin_menuNamePath(
    ) {
        return [
            'orders',
            'new',
        ];
    }
    /* CBAdmin_menuNamePath() */



    /**
     * @return void
     */
    static function
    CBAdmin_render(
    ): void {
        CBHTMLOutput::pageInformation()->title = 'New Orders';

        CB_CBAdmin_NewOrders::deleteExpiredUncompletedOrders();

        /**
         * Get the order IDs of new orders.
         */
        $SQL = <<<EOT

            SELECT
            LOWER(HEX(archiveId))

            FROM
            SCOrders

            WHERE
            isArchived = b'0' AND
            isAuthorized = b'1'

        EOT;

        $orderIDs = CBDB::SQLToArray(
            $SQL
        );

        $models = CBModels::fetchModelsByID(
            $orderIDs
        );

        usort(
            $models,
            function ($a, $b) {
                $orderRowA = CBModel::valueAsInt(
                    $a,
                    'orderRowId'
                );

                $orderRowB = CBModel::valueAsInt(
                    $b,
                    'orderRowId'
                );

                return $orderRowA <=> $orderRowB;
            }
        );

        ?>

        <h1 class="CBUI_title1">New Orders</h1>

        <?php

        CBView::render(
            (object)[
                'className' => 'SCOrderListView',
                'orderModels' => $models,
            ]
        );
    }
    /* CBAdmin_render() */



    /* -- CBInstall interfaces -- */



    /**
     * @return void
     */
    static function
    CBInstall_install(
    ): void {
        $ordersMenuUpdater = new CBModelUpdater(
            SCOrdersAdminMenu::getModelCBID()
        );

        $ordersMenuSpec = $ordersMenuUpdater->getSpec();

        $ordersMenuItemSpecs = CBMenu::getMenuItems(
            $ordersMenuSpec
        );

        $newOrdersMenuItemSpec = CBModel::createSpec(
            'CBMenuItem'
        );

        $newOrdersMenuItemSpec->name = 'new';
        $newOrdersMenuItemSpec->text = 'New';

        $newOrdersMenuItemSpec->URL = CBAdmin::getAdminPageURL(
            'CB_CBAdmin_NewOrders'
        );

        array_push(
            $ordersMenuItemSpecs,
            $newOrdersMenuItemSpec
        );

        CBMenu::setMenuItems(
            $ordersMenuSpec,
            $ordersMenuItemSpecs
        );

        CBDB::transaction(
            function () use (
                $ordersMenuUpdater
            ) {
                $ordersMenuUpdater->save2();
            }
        );
    }
    /* CBInstall_install() */



    /**
     * @return [string]
     */
    static function
    CBInstall_requiredClassNames(
    ): array {
        return [
            'SCOrdersAdminMenu',
        ];
    }
    /* CBInstall_requiredClassNames() */



    /* -- functions -- */



    /**
     * @return void
     */
    private static function
    deleteExpiredUncompletedOrders(
    ): void {

        /**
         * Uncompleted orders expire after 24 hours. The PayPal documentation says
         * that secure tokens expire after 30 minutes, however, there's no need to
         * rush deletion in our database.
         */

        $twentyFourHoursAgo = time() - (
            60 /* seconds */ * 60 /* minutes */ * 24 /* hours */
        );

        $SQL = <<<EOT

            SELECT
            LOWER(HEX(archiveId))

            FROM
            SCOrders

            WHERE
            isAuthorized = b'0' AND
            created < {$twentyFourHoursAgo}

        EOT;

        $expiredOrderIDs = CBDB::SQLToArray(
            $SQL
        );

        $timeout = time() + 2;

        foreach (
            $expiredOrderIDs as $orderID
        ) {
            CBDB::transaction(
                function () use (
                    $orderID
                ) {
                    $orderIDAsSQL = CBID::toSQL(
                        $orderID
                    );

                    $SQL = <<<EOT

                        DELETE FROM
                        SCOrders

                        WHERE
                        archiveId = {$orderIDAsSQL}

                    EOT;

                    Colby::query(
                        $SQL
                    );

                    CBModels::deleteByID(
                        $orderID
                    );
                }
            );

            if (
                time() > $timeout
            ) {
                break;
            }
        }
        /* foreach */
    }
    /* deleteExpiredUncompletedOrders() */

}

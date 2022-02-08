<?php

final class
CB_CBAdmin_ArchivedOrders {

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
            'archived',
        ];
    }
    /* CBAdmin_menuNamePath() */



    /**
     * @return void
     */
    static function
    CBAdmin_render(
    ): void {
        CBHTMLOutput::pageInformation()->title = 'Archived Orders';

        $monthNames = [
            'January',
            'February',
            'March',
            'April',
            'May',
            'June',
            'July',
            'August',
            'September',
            'October',
            'November',
            'December',
        ];

        if (
            !isset($_GET['month'])
        ) {
            $sql = <<<EOT

                SELECT
                createdYearMonth,
                COUNT(*) AS count

                FROM
                SCOrders

                WHERE
                isArchived = b'1'

                GROUP BY
                createdYearMonth

                ORDER BY
                createdYearMonth DESC

            EOT;

            $result = Colby::query(
                $sql
            );

            if (
                $result->num_rows
            ) {
                ?>

                <h1 class="CBUI_title1">Archived Orders</h1>

                <div class="CBUI_sectionContainer">
                    <div class="CBUI_section">

                        <?php

                        while (
                            $row = $result->fetch_object()
                        ) {
                            $href = (
                                CBAdmin::getAdminPageURL(
                                    'CB_CBAdmin_ArchivedOrders'
                                ) .
                                "&month={$row->createdYearMonth}"
                            );

                            $year = substr(
                                $row->createdYearMonth,
                                0,
                                4
                            );

                            $month = substr(
                                $row->createdYearMonth,
                                4,
                                2
                            );

                            $monthName = $monthNames[$month - 1];

                            ?>

                            <a
                                class="CBUI_sectionItem"
                                href="<?= $href ?>"
                            >
                                <div class="CBUI_sectionItemPart_titleDescription">
                                    <div>
                                        <?= $monthName, ' ', $year ?>
                                    </div>
                                    <div>
                                        <?= $row->count ?>
                                        <?= $row->count == 1 ? 'Order' : 'Orders' ?>
                                    </div>
                                </div>
                            </a>

                            <?php
                        }

                        ?>

                    </div>
                </div>

                <?php
            }

            $result->free();
        } else {
            $createdYearMonth = $_GET['month'];
            $expression = '/^([0-9]{4})([0-9]{2})$/';

            if (
                !preg_match(
                    $expression,
                    $createdYearMonth,
                    $matches
                )
            ) {
                throw new InvalidArgumentException(
                    'month'
                );
            }

            $year = $matches[1];
            $month = $matches[2];

            if (
                $month < 0 ||
                $month > 12
            ) {
                throw new InvalidArgumentException(
                    'month'
                );
            }

            $monthName = $monthNames[$month - 1];

            $createdYearMonthAsSQL = CBDB::stringToSQL(
                $createdYearMonth
            );

            /**
             * Get the list of orders.
             */

            $SQL = <<<EOT

                SELECT
                LOWER(HEX(archiveId))

                FROM
                SCOrders

                WHERE
                isArchived = b'1' AND
                createdYearMonth = {$createdYearMonthAsSQL}

            EOT;

            $orderIDs = CBDB::SQLToArray(
                $SQL
            );

            $models = CBModels::fetchModelsByID(
                $orderIDs
            );

            usort(
                $models,
                function (
                    $a,
                    $b
                ) {
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


            <h1 class="CBUI_title1">
                Archived Orders for <?= "{$monthName} {$year}" ?>
            </h1>

            <?php

            CBView::render(
                (object)[
                    'className' => 'SCOrderListView',
                    'orderModels' => $models,
                ]
            );
        }
        /* else */
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

        $archivedOrdersMenuItemSpec = CBModel::createSpec(
            'CBMenuItem'
        );

        $archivedOrdersMenuItemSpec->name = 'archived';
        $archivedOrdersMenuItemSpec->text = 'Archived';

        $archivedOrdersMenuItemSpec->URL = CBAdmin::getAdminPageURL(
            'CB_CBAdmin_ArchivedOrders'
        );

        array_push(
            $ordersMenuItemSpecs,
            $archivedOrdersMenuItemSpec
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
            'CB_CBAdmin_NewOrders',
            'SCOrdersAdminMenu',
        ];
    }
    /* CBInstall_requiredClassNames() */

}

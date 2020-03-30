<?php

$isAdministrator = CBUserGroup::userIsMemberOfUserGroup(
    ColbyUser::getCurrentUserCBID(),
    'CBAdministratorsUserGroup'
);

if (!$isAdministrator) {
    return include cbsysdir() . '/handlers/handle-authorization-failed.php';
}

$info = CBHTMLOutput::pageInformation();
$info->classNameForPageSettings = 'CBPageSettingsForAdminPages';
$info->title = 'Archived Orders';
$info->selectedMenuItemNames = ['orders', 'archived'];

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

CBHTMLOutput::begin();
CBHTMLOutput::requireClassName('CBUI');
CBHTMLOutput::addCSSURL(SCLibraryURL . '/handlers/handle,admin,orders.css');

CBView::render(
    (object)[
        'className' => 'CBAdminPageMenuView',
    ]
);

if (!isset($_GET['month'])) {
    $sql = <<<EOT

        SELECT      createdYearMonth,
                    COUNT(*) AS count
        FROM        SCOrders
        WHERE       isArchived = b'1'
        GROUP BY    createdYearMonth
        ORDER BY    createdYearMonth DESC

    EOT;

    $result = Colby::query($sql);

    if ($result->num_rows) {
        ?>

        <main class="CBUIRoot SCOrdersPage">

            <h1>Archived Orders</h1>

            <div class="CBUI_sectionContainer">
                <div class="CBUI_section">

                    <?php

                    while ($row = $result->fetch_object()) {
                        $href =
                        cbsiteurl()
                        . "/admin/orders/archived/?month={$row->createdYearMonth}";

                        $year = substr($row->createdYearMonth, 0, 4);
                        $month = substr($row->createdYearMonth, 4, 2);
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

        </main>

        <?php
    }

    $result->free();
} else {
    $createdYearMonth = $_GET['month'];
    $expression = '/^([0-9]{4})([0-9]{2})$/';

    if (!preg_match($expression, $createdYearMonth, $matches)) {
        throw new InvalidArgumentException('month');
    }

    $year = $matches[1];
    $month = $matches[2];

    if ($month < 0 || $month > 12) {
        throw new InvalidArgumentException('month');
    }

    $monthName = $monthNames[$month - 1];
    $createdYearMonthAsSQL = CBDB::stringToSQL($createdYearMonth);

    /**
     * Get the list of orders.
     */

    $SQL = <<<EOT

        SELECT      LOWER(HEX(archiveId))
        FROM        SCOrders
        WHERE       isArchived = b'1' AND
                    createdYearMonth = {$createdYearMonthAsSQL}

    EOT;

    $orderIDs = CBDB::SQLToArray($SQL);
    $models = CBModels::fetchModelsByID($orderIDs);

    usort(
        $models,
        function ($a, $b) {
            $orderRowA = CBModel::valueAsInt($a, 'orderRowId');
            $orderRowB = CBModel::valueAsInt($b, 'orderRowId');

            return $orderRowA <=> $orderRowB;
        }
    );

    ?>

    <main class="CBUIRoot SCOrdersPage">
        <h1>Archived Orders for <?php echo "{$monthName} {$year}"; ?></h1>

        <?php

        CBView::render(
            (object)[
                'className' => 'SCOrderListView',
                'orderModels' => $models,
            ]
        );

        ?>

    </main>

    <?php
}

CBView::render(
    (object)[
        'className' => 'CBAdminPageFooterView',
    ]
);

CBHTMLOutput::render();

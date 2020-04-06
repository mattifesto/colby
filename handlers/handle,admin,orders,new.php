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
$info->title = 'New Orders';
$info->selectedMenuItemNames = ['orders', 'new'];

CBHTMLOutput::begin();
CBHTMLOutput::requireClassName('CBUI');
CBHTMLOutput::addCSSURL(SCLibraryURL . '/handlers/handle,admin,orders.css');

CBView::render(
    (object)[
        'className' => 'CBAdminPageMenuView',
    ]
);

deleteExpiredUncompletedOrders();

/**
 * Get the order IDs of new orders.
 */
$SQL = <<<EOT

    SELECT      LOWER(HEX(archiveId))
    FROM        SCOrders
    WHERE       isArchived = b'0' AND
                isAuthorized = b'1'

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
    <h1>New Orders</h1>

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

CBView::render(
    (object)[
        'className' => 'CBAdminPageFooterView',
    ]
);

CBHTMLOutput::render();



/* -- functions -- -- -- -- -- */



/**
 * @return void
 */
function deleteExpiredUncompletedOrders(): void {

    /**
     * Uncompleted orders expire after 24 hours. The PayPal documentation says
     * that secure tokens expire after 30 minutes, however, there's no need to
     * rush deletion in our database.
     */

    $twentyFourHoursAgo = time() - (
        60 /* seconds */ * 60 /* minutes */ * 24 /* hours */
    );

    $SQL = <<<EOT

        SELECT  LOWER(HEX(archiveId))
        FROM    SCOrders
        WHERE   isAuthorized = b'0' AND
                created < {$twentyFourHoursAgo}

    EOT;

    $expiredOrderIDs = CBDB::SQLToArray($SQL);
    $timeout = time() + 2;

    foreach ($expiredOrderIDs as $orderID) {
        CBDB::transaction(
            function () use ($orderID) {
                $orderIDAsSQL = CBID::toSQL($orderID);

                $SQL = <<<EOT

                    DELETE FROM SCOrders
                    WHERE archiveId = {$orderIDAsSQL}

                EOT;

                Colby::query($SQL);
                CBModels::deleteByID($orderID);
            }
        );

        if (time() > $timeout) {
            break;
        }
    }
}
/* deleteExpiredUncompletedOrders() */

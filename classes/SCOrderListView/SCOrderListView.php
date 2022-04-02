<?php

final class
SCOrderListView
{
    /* -- CBHTMLOutput interfaces -- */



    /**
     * @return [string]
     */
    static function
    CBHTMLOutput_CSSURLs(
    ): array
    {
        return
        [
            Colby::flexpath(
                __CLASS__,
                'v675.68.css',
                scliburl()
            ),
        ];
    }
    // CBHTMLOutput_CSSURLs()



    /**
     * @return [string]
     */
    static function
    CBHTMLOutput_requiredClassNames(
    ): array
    {
        return
        [
            'CBUI',
        ];
    }
    // CBHTMLOutput_requiredClassNames()



    /* -- CBView interfaces -- */

    /**
     * @param object $viewModel
     *
     *      {
     *          orderModels: [object]
     *      }
     *
     * @return void
     */
    static function
    CBView_render(
        stdClass $viewModel
    ): void
    {
        $orderModels =
        CBModel::valueToArray(
            $viewModel,
            'orderModels'
        );

        if (
            empty($orderModels)
        ) {
            return;
        }

        ?>

        <div class="SCOrderListView CBUI_sectionContainer">
            <div class="CBUI_section">

                <?php

                foreach (
                    $orderModels as $orderModel
                ) {
                    SCOrderListView::renderOrderSectionItem(
                        $orderModel
                    );
                }

                ?>

            </div>
        </div>

        <div class="SCOrderListView CBUI_sectionContainer">
            <div class="CBUI_section">

                <?php

                $totalAuthorizedCents =
                array_reduce(
                    $orderModels,
                    function (
                        $accumulatedAuthorizedCents,
                        $orderModel
                    ) {
                        $authorizedCents =
                        CBModel::valueAsInt(
                            $orderModel,
                            'orderPaymentAuthorizedAmountInCents'
                        ) ??
                        0;

                        return
                        $accumulatedAuthorizedCents +
                        $authorizedCents;
                    },
                    0
                );

                ?>

                <div class="CBUI_sectionItem">
                    <div class="
                        CBUI_sectionItemPart_titleDescription
                        CBUI_flexGrow
                    ">
                        <div class="CBUI_ellipsis">
                            Total Authorized
                        </div>
                    </div>
                    <div class="
                        CBUI_sectionItemPart_titleDescription
                        CBUI_flexNone
                    ">
                        <div>
                            $<?=

                            CBConvert::centsToDollars(
                                $totalAuthorizedCents
                            )

                            ?>
                        </div>
                    </div>
                </div>

                <?php

                $totalCapturedCents =
                array_reduce(
                    $orderModels,
                    function (
                        $accumulatedCapturedCents,
                        $orderModel
                    ) {
                        $capturedCents =
                        CBModel::valueAsInt(
                            $orderModel,
                            'orderPaymentCapturedAmountInCents'
                        ) ??
                        0;

                        return
                        $accumulatedCapturedCents +
                        $capturedCents;
                    },
                    0
                );

                ?>

                <div class="CBUI_sectionItem">
                    <div class="
                        CBUI_sectionItemPart_titleDescription
                        CBUI_flexGrow
                    ">
                        <div class="CBUI_ellipsis">
                            Total Captured
                        </div>
                    </div>
                    <div class="
                        CBUI_sectionItemPart_titleDescription
                        CBUI_flexNone
                    ">
                        <div>
                            $<?=

                            CBConvert::centsToDollars(
                                $totalCapturedCents
                            )

                            ?>
                        </div>
                    </div>
                </div>

            </div>
        </div>

        <?php
    }
    // CBView_render()



    /**
     * @param object $orderModel
     *
     * @return void
     */
    static function
    renderOrderSectionItem(
        stdClass $orderModel
    ): void
    {
        $orderID =
        $orderModel->ID;

        $href =
        cbsiteurl() .
        "/admin/?c=SCOrderInspector&ID={$orderID}";

        /* wholesale */

        $sectionItemClassNames =
        'CBUI_sectionItem';

        $isWholesaleOrder =
        !empty(
            $orderModel->isWholesale
        );

        if (
            $isWholesaleOrder
        ) {
            $sectionItemClassNames .=
            ' SCOrderListView_wholesale';
        }


        /* amount section item part */

        $amountPartClassNames =
        implode(
            ' ',
            [
                'CBUI_sectionItemPart_titleDescription',
                'CBUI_flexNone',
            ]
        );

        $capturedCents =
        CBModel::valueAsInt(
            $orderModel,
            'orderPaymentCapturedAmountInCents'
        );

        if (
            $capturedCents === null
        ) {
            $amountPartClassNames .=
            ' SCOrderListView_authorized';

            $authorizedCents =
            CBModel::valueAsInt(
                $orderModel,
                'orderPaymentAuthorizedAmountInCents'
            ) ??
            0;

            $amountInDollars =
            CBConvert::centsToDollars(
                $authorizedCents
            );
        }

        else
        {
            $amountPartClassNames .=
            ' SCOrderListView_captured';

            $amountInDollars =
            CBConvert::centsToDollars(
                $capturedCents
            );
        }


        /* amount section item part: payment method */

        $orderPaymentMethod =
        CBModel::valueToString(
            $orderModel,
            'orderPaymentMethod'
        );

        if (
            $orderPaymentMethod === 'Net30'
        ) {
            $amountPartClassNames .=
            ' SCOrderListView_net30';
        }

        else
        {
            $amountPartClassNames .=
            ' SCOrderListView_creditCard';
        }

        $orderRowNumber =
        CBModel::valueAsInt(
            $orderModel,
            'orderRowId'
        ) ??
        'error';

        $createdTimestamp =
        CBModel::valueAsInt(
            $orderModel,
            'orderCreated'
        );

        $name =
        CBModel::valueToString(
            $orderModel,
            'shipOrderToFullName'
        );

        $email =
        CBModel::valueToString(
            $orderModel,
            'shipOrderToEmail'
        );

        ?>

        <a
            class="<?= $sectionItemClassNames ?>"
            href="<?= cbhtml($href) ?>"
        >
            <div class="CBUI_sectionItemPart_titleDescription CBUI_flexGrow">
                <div class="CBUI_ellipsis">
                    <?=

                    cbhtml(
                        $name
                    ),
                    ' (',
                    cbhtml(
                        $email
                    ),
                    ')'

                    ?>
                </div>
                <div class="CBUI_ellipsis">
                    <?php

                        echo
                        cbhtml(
                            $orderRowNumber
                        ),
                        ' | ';

                        if (
                            $isWholesaleOrder
                        ) {
                            echo
                            'wholesale | ';
                        }

                    ?>
                    <span
                        class="time compact"
                        data-timestamp="<?= $createdTimestamp * 1000 ?>"
                    >
                    </span>
                </div>
            </div>
            <div class="<?= $amountPartClassNames ?>">
                <div>
                    $<?= cbhtml($amountInDollars) ?>
                </div>
            </div>
        </a>

        <?php
    }
    // renderOrderSectionItem()

}

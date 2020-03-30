<?php

class SCOrderConfirmationEmail {

    /* -- functions -- -- -- -- -- */



    /**
     * @param model $model (SCOrder)
     *
     * @return void
     */
    static function send(stdClass $model): void {
        $shipOrderToEmail = CBModel::valueToString(
            $model,
            'shipOrderToEmail'
        );

        $shipOrderToFullName = CBModel::valueToString(
            $model,
            'shipOrderToFullName'
        );

        $messageSubject = (
            'Your ' .
            CBSitePreferences::siteName() .
            ' order.'
        );

        $messageAsPlaintext = SCOrderConfirmationEmail::messageText($model);
        $messageAsHTMLDocument = SCOrderConfirmationEmail::messageHTML($model);

        CBEmail::sendTextAndHTML(
            $shipOrderToEmail,
            $shipOrderToFullName,
            $messageSubject,
            $messageAsPlaintext,
            $messageAsHTMLDocument
        );

        /**
         * Update the order document to indicate that the email was sent.
         */

        $spec = CBModels::fetchSpecByIDNullable($model->ID);
        $spec->orderEmailWasSent = true;

        CBDB::transaction(
            function () use ($spec) {
                CBModels::save($spec);
            }
        );
    }
    /* send() */



    /**
     * @param model $model (SCOrder)
     *
     * @return string
     */
    static function messageHTML(stdClass $model): string {
        $shipOrderToFullNameHTML = cbhtml(
            CBModel::valueToString($model, 'shipOrderToFullName')
        );

        $shipOrderToAddressLine1HTML = cbhtml(
            CBModel::valueToString($model, 'shipOrderToAddressLine1')
        );

        $shipOrderToAddressLine2HTML = cbhtml(
            CBModel::valueToString($model, 'shipOrderToAddressLine2')
        );

        $shipOrderToCityHTML = cbhtml(
            CBModel::valueToString($model, 'shipOrderToCity')
        );

        $shipOrderToStateProvinceOrRegionHTML = cbhtml(
            CBModel::valueToString($model, 'shipOrderToStateProvinceOrRegion')
        );

        $shipOrderToPostalCodeHTML = cbhtml(
            CBModel::valueToString($model, 'shipOrderToPostalCode')
        );

        $shipOrderToCountryNameAsHTML = cbhtml(
            CBModel::valueToString($model, 'shipOrderToCountryName')
        );

        $shipOrderWithSpecialInstructionsHTML = cbhtml(
            CBModel::valueToString($model, 'shipOrderWithSpecialInstructions')
        );

        /* address lines */

        $addressLines = [
            $shipOrderToFullNameHTML,
            $shipOrderToAddressLine1HTML,
        ];

        if ($shipOrderToAddressLine2HTML) {
            array_push(
                $addressLines,
                $shipOrderToAddressLine2HTML
            );
        }

        array_push(
            $addressLines,
            (
                $shipOrderToCityHTML .
                ', ' .
                $shipOrderToStateProvinceOrRegionHTML .
                ' ' .
                $shipOrderToPostalCodeHTML
            )
        );

        if ($shipOrderToCountryNameAsHTML) {
            array_push(
                $addressLines,
                $shipOrderToCountryNameAsHTML
            );
        }

        $sCell =
            'padding: 20px 5px;' .
            'vertical-align: top;' .
            'border-bottom: 1px solid gray;';

        $sCellRight = (
            $sCell .
            ' ' .
            'text-align: right;'
        );

        $sTotalCell = (
            'padding: 10px 5px 5px; ' .
            'border-top: 1px solid #7f7f7f;'
        );

        $sTotalCellRight = (
            $sTotalCell .
            ' ' .
            'text-align: right;'
        );

        $sSection = (
            'padding: 5px; ' .
            'margin: 30px 0px; ' .
            'background-color: #3f7f3f; ' .
            'color: white; ' .
            'font-weight: bold;'
        );

        ob_start();

        try {

            echo SCOrderConfirmationEmail::messageHTMLBegin();

            ?>


            <p>
                Thank you for your order with
                <a href="<?= cbsiteurl() ?>/">
                    <?= cbhtml(CBSitePreferences::siteName()) ?>
                </a>

            <?php

            $orderSubtotalInDollars = CBConvert::centsToDollars(
                CBModel::valueAsInt($model, 'orderSubtotalInCents')
            );

            $orderShippingChargeInDollars = CBConvert::centsToDollars(
                CBModel::valueAsInt($model, 'orderShippingChargeInCents')
            );

            $orderSalesTaxInDollars = CBConvert::centsToDollars(
                CBModel::valueAsInt($model, 'orderSalesTaxInCents')
            );

            $orderTotalInDollars = CBConvert::centsToDollars(
                CBModel::valueAsInt($model, 'orderTotalInCents')
            );

            $message = <<<EOT

                (Order Subtotal (b)): \${$orderSubtotalInDollars}
                ((br))
                (Shipping (b)): \${$orderShippingChargeInDollars}
                ((br))
                (Sales Tax (b)): \${$orderSalesTaxInDollars}
                ((br))
                (Order Total (b)): \${$orderTotalInDollars}

            EOT;

            echo CBMessageMarkup::messageToHTML($message);

            ?>

            <dl>
                <dt><p>Shipping Address</dt>
                <dd>
                    <p><?= implode('<br>', $addressLines) ?>
                </dd>

                <?php

                if ($shipOrderWithSpecialInstructionsHTML) {
                    ?>

                    <dt><p>Special Instructions</dt>
                    <dd><p><?= $shipOrderWithSpecialInstructionsHTML ?></dd>

                    <?php
                }

                ?>

            </dl>

            <dl>
                <?php

                $orderItems = CBModel::valueToArray($model, 'orderItems');
                $itemNumber = 1;

                foreach ($orderItems as $orderItem) {
                    echo "<dt>Item {$itemNumber}</dt><dd>";
                    echo SCCartItem::toHTML($orderItem);
                    echo '<dd>';
                    $itemNumber += 1;
                }

                ?>
            </dl>

            <?php

            echo SCOrderConfirmationEmail::messageHTMLEnd();
        } catch (Throwable $throwable) {
            ob_end_clean();

            throw $throwable;
        }

        return ob_get_clean();
    }
    /* messageHTML() */



    /**
     * @TODO 2018_11_02
     *
     *      This duplicates functionality of the CBHTMLOutput class because that
     *      is one of the oldest classes and it has not ever been used to
     *      generate a second HTML document, one not meant for immediate
     *      presentation, during a request.
     *
     *      As future changes are made, try to share functions from that class.
     *
     * @return string
     */
    static function messageHTMLBegin(): string {
        ob_start();

        $bodyStyles = implode(
            '; ',
            [
                'font-family: \'Helvetica Neue\', Helvetica, Arial, sans-serif',
                'font-size: 18px',
                'padding: 20px'
            ]
        );

        $CBContentStyleSheet = file_get_contents(
            Colby::flexpath('CBContentStyleSheet', 'css', cbsysurl())
        );

        try {
            ?>

            <!doctype html>
            <html lang="en">
                <head>
                    <meta charset="UTF-8">
                    <title>Receipt</title>
                    <meta name="description" content="">
                    <style>
                        * {
                            margin: 0;
                        }

                        <?= $CBContentStyleSheet ?>
                    </style>
                </head>
                <body class="CBContentStyleSheet" style="<?= $bodyStyles ?>">

            <?php
        } catch (Throwable $throwable) {
            ob_end_clean();

            throw $throwable;
        }

        return ob_get_clean();
    }
    /* messageHTMLBegin() */



    /**
     * @TODO 2018_11_02
     *
     *      This duplicates functionality of the CBHTMLOutput class because that
     *      is one of the oldest classes and it has not ever been used to
     *      generate a second HTML document, one not meant for immediate
     *      presentation, during a request.
     *
     *      As future changes are made, try to share functions from that class.
     *
     * @return string
     */
    static function messageHTMLEnd(): string {
        ob_start();

        try {
            ?>

                </body>
            </html>

            <?php
        } catch (Throwable $throwable) {
            ob_end_clean();

            throw $throwable;
        }

        return ob_get_clean();
    }
    /* messageHTMLEnd() */



    /**
     * @param object $model (SCOrder)
     *
     * @return string
     */
    static function messageText(stdClass $orderModel): string {
        $shipOrderToFullName = CBModel::valueToString(
            $orderModel,
            'shipOrderToFullName'
        );
        $shipOrderToAddressLine1 = CBModel::valueToString(
            $orderModel,
            'shipOrderToAddressLine1'
        );
        $shipOrderToAddressLine2 = CBModel::valueToString(
            $orderModel,
            'shipOrderToAddressLine2'
        );
        $shipOrderToCity = CBModel::valueToString(
            $orderModel,
            'shipOrderToCity'
        );
        $shipOrderToStateProvinceOrRegion = CBModel::valueToString(
            $orderModel,
            'shipOrderToStateProvinceOrRegion'
        );
        $shipOrderToPostalCode = CBModel::valueToString(
            $orderModel,
            'shipOrderToPostalCode'
        );
        $shipOrderWithSpecialInstructions = CBModel::valueToString(
            $orderModel,
            'shipOrderWithSpecialInstructions'
        );

        ob_start();

        try
        {
            echo implode(
                ' ',
                [
                    'Thank you for your order with',
                    CBSitePreferences::siteName(),
                ]
            ), "\n";

            echo cbsiteurl(), "\n\n";

            echo (
                'Summary:' .
                "\n\n" .
                '  Subtotal:  ' .
                SCOrderConfirmationEmail::valueToRightAlignedDollarString(
                    $orderModel,
                    'orderSubtotalInCents'
                ) .
                "\n" .
                '  Shipping:  ' .
                SCOrderConfirmationEmail::valueToRightAlignedDollarString(
                    $orderModel,
                    'orderShippingChargeInCents'
                ) .
                "\n" .
                '  Sales Tax: ' .
                SCOrderConfirmationEmail::valueToRightAlignedDollarString(
                    $orderModel,
                    'orderSalesTaxInCents'
                ) .
                "\n" .
                '  Total:     ' .
                SCOrderConfirmationEmail::valueToRightAlignedDollarString(
                    $orderModel,
                    'orderTotalInCents'
                ) .
                "\n\n"
            );


            echo "Shipping Address:\n\n";

            echo '  ', $shipOrderToFullName, "\n";
            echo '  ', $shipOrderToAddressLine1, "\n";
            if ($shipOrderToAddressLine2) {
                echo '  ', $shipOrderToAddressLine2, "\n";
            }
            echo '  ', $shipOrderToCity,
                 ', ',
                 $shipOrderToStateProvinceOrRegion,
                 ' ',
                 $shipOrderToPostalCode,
                 "\n";

            if ($shipOrderWithSpecialInstructions) {
                echo "\nSpecial Instructions:\n\n",
                     $shipOrderWithSpecialInstructions,
                     "\n";
            }

            echo "\nOrder Details:\n\n";

            $orderItems = CBModel::valueToArray($orderModel, 'orderItems');
            $itemNumber = 1;

            foreach ($orderItems as $orderItem) {
                echo implode(
                    '',
                    [
                        "---------- ----------\n\n",
                        SCCartItem::toText($orderItem),
                        "\n\n\n",
                    ]
                );

                $itemNumber += 1;
            }
        } catch (Exception $exception) {
            ob_end_clean();

            throw $exception;
        }

        return ob_get_clean();
    }
    /* messageText() */



    /**
     * @param mixed $model
     * @param string $keyPath
     */
    static function valueToRightAlignedDollarString(
        $model,
        string $keyPath
    ): string {
        $valueAsDollarString = (
            "$" .
            CBConvert::centsToDollars(
                CBModel::valueAsInt($model, $keyPath) ?? 0
            )
        );

        return str_pad(
            $valueAsDollarString,
            15,
            ' ',
            STR_PAD_LEFT
        );
    }
    /* valueToRightAlignedDollarString() */

}

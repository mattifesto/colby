<?php

/**
 *
 */
final class SCOrderConfirmationEmailTests {

    /* -- CBTest interfaces -- -- -- -- -- */



    /**
     * @return [object]
     */
    static function CBTest_getTests(
    ): array {
        return [
            (object)[
                'name' => 'messageHTML',
                'type' => 'server',
            ],
        ];
    }
    /* CBTest_getTests() */



    /* -- tests -- -- -- -- -- */



    /**
     * @return object
     */
    static function CBTest_messageHTML(
    ): stdClass {
        $testOrderSpec = (object)[
            'className' => 'SCOrder',
            'kindClassName' => 'SCOrderConfirmationEmailTests_OrderKind1',
        ];

        $testOrderPreparedSpec = SCOrder::prepare(
            $testOrderSpec
        );

        $testOrderModel = CBModel::build(
            $testOrderPreparedSpec
        );

        $HTML = SCOrderConfirmationEmail::messageHTML(
            $testOrderModel
        );

        $HTMLAsCBMessage = CBMessageMarkup::stringToMessage(
            $HTML
        );

        return (object)[
            'succeeded' => true,
            'message' => <<<EOT

                Test Order Email HTML:

                --- pre\n{$HTMLAsCBMessage}
                ---
            EOT,
        ];
    }
    /* CBTest_messageHTML() */

}
/* SCOrderConfirmationEmailTests */



/**
 *
 */
final class SCOrderConfirmationEmailTests_OrderKind1 {

    /* -- SCOrderKind interfaces -- -- -- -- -- */




    /**
     * @return string
     */
    static function SCOrderKind_defaultShippingMethod(): string {
        return 'Second Day Air';
    }



    /**
     * @param object $orderSpec
     *
     * @return int
     */
    static function SCOrderKind_salesTaxInCents(
        stdClass $orderSpec
    ): int {
        return 200;
    }

    /**
     * @param object $orderSpec
     *
     * @return int
     */
    static function SCOrderKind_shippingChargeInCents(
        stdClass $orderSpec
    ): int {
        return 0;
    }

}
/* SCOrder_Tests_OrderKind2 */

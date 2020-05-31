<?php

final class SCOrder {

    /* -- CBAjax interfaces ---------- */



    /**
     * @param object $args
     *
     *      {
     *          orderID: string
     *          text: string
     *      }
     *
     * @return object
     */
    static function CBAjax_addNote(stdClass $args): stdClass {
        $orderID = CBModel::valueAsID($args, "orderID");

        if ($orderID === null) {
            throw new Exception(
                'No order ID was provided.'
            );
        }

        $text = CBModel::valueToString($args, 'text');

        if (trim($text) === '') {
            throw new Exception(
                'The text in the note is empty.'
            );
        }

        $noteSpec = (object)[
            'className' => 'CBNote',
            'text' => $text,
            'timestamp' => time(),
            'userID' => ColbyUser::getCurrentUserCBID(),
        ];

        $updater = CBModelUpdater::fetch(
            (object)[
                'ID' => $orderID,
            ]
        );

        if ($updater->working->className !== "SCOrder") {
            throw new CBExceptionWithValue(
                'The target model is not an order.',
                $updater->working,
                '121538e14d966020ca42d71b69d6771e25fbde1a'
            );
        }

        $notes = CBModel::valueToArray(
            $updater->working,
            'notes'
        );

        array_push(
            $notes,
            $noteSpec
        );

        $updater->working->notes = $notes;

        CBModelUpdater::save($updater);

        return CBModel::build($noteSpec);
    }
    /* CBAjax_addNote() */



    /**
     * @return string
     */
    static function CBAjax_addNote_getUserGroupClassName(): string {
        return 'CBAdministratorsUserGroup';
    }



    /**
     * @param object $args
     *
     *      {
     *          orderID: ID
     *          amountInCents: int
     *
     *          captureWasManual: bool (optional)
     *
     *              If this is set to true, it means that the order has already
     *              been captured manually on the Stripe website. The capture
     *              will be recorded, but will not be attempted.
     *      }
     *
     * @return void
     */
    static function CBAjax_capture(stdClass $args): void {
        $orderID = CBModel::valueAsCBID(
            $args,
            'orderID'
        );

        $model = CBModels::fetchModelByIDNullable(
            $orderID
        );

        $orderPaymentMethod = CBModel::valueToString(
            $model,
            'orderPaymentMethod'
        );

        if ($orderPaymentMethod !== 'Stripe') {
            throw new CBExceptionWithValue(
                'This order cannot be captured.',
                $model,
                'b423cc4134f86b21aa16491be1d12a3be1a227d5'
            );
        }

        $amountInCents = CBModel::valueAsInt(
            $args,
            'amountInCents'
        );

        $captureWasManual = CBModel::valueToBool(
            $args,
            'captureWasManual'
        );

        if (!$captureWasManual) {
            $stripeChargeID = CBModel::valueToString(
                $model,
                'orderPaymentStripeChargeId'
            );

            $stripeResultObject = SCStripe::call(
                (object)[
                    'apiURL' => (
                        'https://api.stripe.com/v1/charges/' .
                        $stripeChargeID .
                        '/capture'
                    ),

                    'apiKey' => CBModel::valueToString(
                        CBModelCache::fetchModelByID(
                            SCStripePreferences::ID()
                        ),
                        'liveSecretKey'
                    ),

                    'apiArgs' => (object)[
                        'amount' => $amountInCents,
                    ],
                ]
            );

            $stripeErrorObject = CBModel::valueAsObject(
                $stripeResultObject,
                'error'
            );

            if ($stripeErrorObject !== null) {
                throw new CBExceptionWithValue(
                    CBModel::valueToString(
                        $stripeErrorObject,
                        'message'
                    ),
                    $stripeResultObject,
                    '33998757d4b4920edd3f817190aab04c93238e23'
                );
            }
        }

        $spec = CBModels::fetchSpecByIDNullable(
            $orderID
        );

        $spec->orderPaymentCaptured = time();
        $spec->orderPaymentCapturedAmountInCents = $amountInCents;

        $spec->orderPaymentCapturedByUserCBID = (
            ColbyUser::getCurrentUserCBID()
        );

        CBDB::transaction(
            function () use ($spec) {
                SCOrder::updateOrdersTableRow($spec);
                CBModels::save($spec);
            }
        );
    }
    /* CBAjax_capture() */



    /**
     * @return string
     */
    static function CBAjax_capture_getUserGroupClassName(): string {
        return 'CBAdministratorsUserGroup';
    }



    /**
     * @param object $args
     *
     * @return object
     */
    static function CBAjax_charge(stdClass $args): stdClass {
        $orderID = CBModel::valueAsID(
            $args,
            'orderID'
        );

        $stripeToken = CBModel::valueToString(
            $args,
            'stripeToken'
        );

        $response = (object)[];

        $model = CBModels::fetchModelByIDNullable($orderID);

        if ($model === null) {
            throw new CBExceptionWithValue(
                'The order model was not found.',
                $args,
                '9955ed4fbc10086085074f96e37cd0cb59ec1c82'
            );
        }

        $orderRowId = CBModel::valueAsInt(
            $model,
            'orderRowId'
        );

        $orderTotalInCents = CBModel::valueAsInt(
            $model,
            'orderTotalInCents'
        );

        $shipOrderToFullName = CBModel::valueToString(
            $model,
            'shipOrderToFullName'
        );

        $shipOrderToEmail = CBModel::valueToString(
            $model,
            'shipOrderToEmail'
        );

        $stripeResultObject = SCStripe::call(
            (object)[
                'apiURL' => 'https://api.stripe.com/v1/charges',

                'apiKey' => CBModel::valueToString(
                    CBModelCache::fetchModelByID(
                        SCStripePreferences::ID()
                    ),
                    'liveSecretKey'
                ),

                'apiArgs' => (object)[
                    'amount' => $orderTotalInCents,

                    'capture' => 'false',

                    'currency' => 'usd',

                    'description' => (
                        "Order {$orderRowId} for {$shipOrderToFullName} " .
                        "({$shipOrderToEmail})"
                    ),

                    'source' => $stripeToken,
                ],
            ]
        );

        $stripeErrorObject = CBModel::valueAsObject(
            $stripeResultObject,
            'error'
        );

        if ($stripeErrorObject !== null) {
            throw new CBExceptionWithValue(
                CBModel::valueToString(
                    $stripeErrorObject,
                    'message'
                ),
                $stripeResultObject,
                '9de8bc2fb72899014c00555dcccb5a248c15cfa5'
            );
        }

        $stripeChargeID = CBModel::valueToString(
            $stripeResultObject,
            'id'
        );

        if ($stripeChargeID === '') {
            throw new CBExceptionWithValue(
                'The charge result object does not have a valid "id" property.',
                $stripeResultObject,
                '432967629fd299b639439c47ca34a97574c6960e'
            );
        }

        $stripeChargeCreatedTimestamp = CBModel::valueAsInt(
            $stripeResultObject,
            'created'
        );

        if ($stripeChargeCreatedTimestamp === null) {
            throw new CBExceptionWithValue(
                'The charge result object does not have a valid "created" ' .
                'property.',
                $stripeResultObject,
                '8f62af784a1a98df2c6c4d67895cacbd11842a70'
            );
        }

        $stripeChangeAmountInCents = CBModel::valueAsInt(
            $stripeResultObject,
            'amount'
        );

        if ($stripeChangeAmountInCents === null) {
            throw new CBExceptionWithValue(
                'The charge result object does not have a valid "amount" ' .
                'property.',
                $stripeResultObject,
                '1e7f7c37823eb8c6a19f24a879e6e74affffb7f9'
            );
        }

        $response->stripeChargeWasSuccessful = true;

        /**
         * Update the order model.
         */

        $spec = CBModels::fetchSpecByIDNullable($orderID);

        CBModel::merge(
            $spec,
            (object)[
                'orderPaymentMethod' => 'Stripe',
                'orderPaymentStripeChargeId' => $stripeChargeID,
                'orderPaymentAuthorized' => $stripeChargeCreatedTimestamp,
                'orderPaymentAuthorizedAmountInCents' => (
                    $stripeChangeAmountInCents
                ),
            ]
        );

        CBDB::transaction(
            function () use ($spec) {
                SCOrder::updateOrdersTableRow($spec);
                CBModels::save($spec);
            }
        );

        /**
         * Send the order confirmation email.
         */

        try {
            $model = CBModels::fetchModelByIDNullable($orderID);

            SCOrderConfirmationEmail::send($model);

            $response->message = (
                "Your card was successfully charged. A receipt has been sent " .
                "to your email address."
            );
        } catch (Throwable $throwable) {
            CBErrorHandler::report($throwable);

            $shipOrderToEmail = CBModel::valueToString(
                $model,
                'shipOrderToEmail'
            );

            /**
             * @TODO 2019_07_28
             *
             *  Add a note to the order.
             */

            $response->message = (
                "Your order was successfully placed, but an error occurred " .
                "when we tried to send an email receipt to your email " .
                "address: {$shipOrderToEmail}. Please save or print this " .
                "page for a record of your order."
            );
        }

        return $response;
    }
    /* CBAjax_charge() */



    /**
     * @return string
     */
    static function CBAjax_charge_getUserGroupClassName(): string {
        return 'CBPublicUserGroup';
    }



    /**
     * This function creates a live order for a website customer on the order
     * page.
     *
     * @param object $args
     *
     *      {
     *          shippingAddress: object
     *          shoppingCart: object
     *      }
     *
     * @return object
     *
     *      {
     *          approvedForNet30: bool
     *          messageAsText: string
     *          orderArchiveId: ID
     *          orderDetails: object
     *          orderSummaryHTML: string
     *          shippingInformationHTML: string
     *          wasCancelled: bool
     *      }
     */
    static function CBAjax_create(
        stdClass $args
    ): stdClass {
        $shippingAddressArgument = CBModel::valueToObject(
            $args,
            'shippingAddress'
        );

        $shoppingCart = CBModel::valueToObject(
            $args,
            'shoppingCart'
        );

        $time = time();
        $response = (object)[];

        /**
         * @TODO 2018_11_21
         *
         *      There is a lot of work that needs to be done to validate and fix
         *      the shipping address fields that should be done in a build
         *      function.
         */


        /* state */

        $state = trim(
            CBModel::valueToString(
                $shippingAddressArgument,
                'state-province-or-region'
            )
        );


        /* SCOrder spec */

        /**
         * kind class name
         *
         * Since this function generates an order spec, it uses the default
         * order kind class name every time. In the future it is likely that a
         * spec will be delivered to this function with the 'kindClassName'
         * property potentially filled in already.
         */

        $orderKindClassName = CBModel::valueAsName(
            CBModelCache::fetchModelByID(
                SCPreferences::ID()
            ),
            'defaultOrderKindClassName'
        );

        if ($orderKindClassName === null) {
            throw new CBException(
                'This website has no default order kind class name.',
                '',
                '0419febee3e06731f6be19e7917b0bb30e44bbad'
            );
        }

        /* generate order ID */

        $orderID = CBID::generateRandomCBID();

        /* create spec */

        $spec = (object)[
            'ID' => $orderID,
            'className' => 'SCOrder',
            'kindClassName' => $orderKindClassName,
            'orderCreated' => $time,
            'orderCreatedYearMonth' => gmdate('Ym', $time),

            'shipOrderToFullName' => CBModel::valueToString(
                $shippingAddressArgument,
                'full-name'
            ),

            'shipOrderToEmail' => CBModel::valueToString(
                $shippingAddressArgument,
                'email-address'
            ),

            'shipOrderToAddressLine1' => CBModel::valueToString(
                $shippingAddressArgument,
                'address-line-1'
            ),

            'shipOrderToAddressLine2' => CBModel::valueToString(
                $shippingAddressArgument,
                'address-line-2'
            ),

            'shipOrderToCity' => CBModel::valueToString(
                $shippingAddressArgument,
                'city'
            ),

            'shipOrderToStateProvinceOrRegion' => $state,

            'shipOrderToPostalCode' => CBModel::valueToString(
                $shippingAddressArgument,
                'zip'
            ),

            'shipOrderToPhone' => CBModel::valueToString(
                $shippingAddressArgument,
                'phone'
            ),

            'shipOrderWithSpecialInstructions' => CBModel::valueToString(
                $shippingAddressArgument,
                'special-instructions'
            ),
        ];


        /**
         * The customer's website user ID is set by the creator of the original
         * spec or left blank if the customer does not have a websites user ID.
         *
         * This must happen before the prepare() function is called because the
         * prepare() function may use the customer's user ID to determine
         * whether the order is a wholesale order.
         */
        $spec->customerHash = ColbyUser::getCurrentUserCBID();


        /**
         * This function is only called for live orders. For other orders, the
         * code will set the "isWholesale" property value to whatever the code
         * wants it to be.
         */
        $spec->isWholesale = SCOrderKind::liveOrderIsWholesale(
            $spec
        );


        /* shipOrderToCountryCode */

        $countryCode = SCOrderKind::countryOptionValueToCountryCode(
            $orderKindClassName,
            trim(
                CBModel::valueToString(
                    $shippingAddressArgument,
                    'country'
                )
            )
        );

        if ($countryCode === null) {
            throw new CBExceptionWithValue(
                CBConvert::stringToCleanLine(<<<EOT

                    The shipping address provided does not have a valid
                    "country" property value.

                EOT),
                $shippingAddressArgument,
                '46211e460871bb6ec14527ef6f58f297f711a8ef'
            );
        } else {
            $spec->shipOrderToCountryCode = $countryCode;
        }


        /* shipOrderToCountryName */

        $countryName = SCOrderKind::countryCodeToCountryName(
            $orderKindClassName,
            $countryCode
        );

        if ($countryName === null) {
            $countryCodeAsJSON = json_encode($countryCode);

            throw new CBExceptionWithValue(
                CBConvert::stringToCleanLine(<<<EOT

                    The shipping address \"country\" property was translated to
                    the country code {$countryCodeAsJSON} which generated a null
                    country name.

                EOT),
                $shippingAddressArgument,
                'eeeaf79ac307e52310e89461fdb043868ffc0da3'
            );
        } else {
            $spec->shipOrderToCountryName = $countryName;
        }


        /* cart items */

        $originalCartItemSpecs = SCShoppingCart::getItems(
            CBModel::valueToObject(
                $args,
                'shoppingCart'
            )
        );

        $updatedCartItemSpecs = SCCartItem::updateSpecs(
            $originalCartItemSpecs
        );

        foreach ($updatedCartItemSpecs as $updatedCartItemSpec) {
            $quantity = SCCartItem::getQuantity(
                $updatedCartItemSpec
            );

            $isNotAvailable = SCCartItem::getIsNotAvailable(
                $updatedCartItemSpec
            );

            if ($quantity === 0 || $isNotAvailable) {
                $message = <<<EOT

                    There is an issue with one of the items in your shopping
                    cart that must be reviewed on the shopping cart page before
                    your order can be completed.

                EOT;

                return (object)[
                    'wasCancelled' => true,
                    'messageAsText' => CBConvert::stringToCleanLine(
                        $message
                    ),
                ];
            }
        }

        $spec->orderItems = $updatedCartItemSpecs;


        /**
         * One by one, the above actions that are suitable to be done in
         * prepare() will be migrated out of this function and into prepare().
         */

        $preparedSpec = SCOrder::prepare($spec);

        /**
         * If the order doesn't meet the minimum subtotal, which is a
         * requirement for orders such as wholesale orders, do not create the
         * order and return a message. Otherwise, create and save the order.
         *
         * The check for minumum subtotal should probably happen in prepare().
         */

        $minimumSubtotalInCents = SCOrderKind::getMinimumSubtotalInCents(
            $preparedSpec
        );

        if ($preparedSpec->orderSubtotalInCents < $minimumSubtotalInCents) {
            $minimumSubtotalInDollars = CBConvert::centsToDollars(
                $minimumSubtotalInCents
            );

            $response->wasCancelled = true;

            $response->messageAsText = (
                CBConvert::stringToCleanLine(<<<EOT

                    Orders must have a subtotal of at least
                    \${$minimumSubtotalInDollars}.

                EOT)
            );
        } else {
            CBDB::transaction(
                function () use ($preparedSpec) {
                    SCOrder::createOrdersTableRow($preparedSpec);
                    CBModels::save($preparedSpec);
                }
            );

            $orderModel = CBModelCache::fetchModelByID(
                $orderID
            );

            $response->orderArchiveId = $orderID;

            $response->orderSummaryHTML = SCOrder::createSummaryHTML(
                $orderModel
            );

            $response->orderCBMessages = SCOrderKind::orderToCBMessages(
                $orderModel
            );

            $response->shippingInformationHTML = (
                SCOrder::createOrderInformationHTML(
                    $orderModel
                )
            );

            $response->orderDetails = SCOrder::orderToCheckoutInformation(
                $orderModel
            );

            $isWholesaleCustomer = CBUserGroup::userIsMemberOfUserGroup(
                ColbyUser::getCurrentUserCBID(),
                'LEWholesaleCustomersUserGroup'
            );

            if ($isWholesaleCustomer) {
                $response->approvedForNet30 =
                LEWholesaleCustomerSettings::currentUserIsApprovedForNet30();
            }
        }

        return $response;
    }
    /* CBAjax_create() */



    /**
     * @return string
     */
    static function CBAjax_create_getUserGroupClassName(): string {
        return 'CBPublicUserGroup';
    }



    /**
     * @return object
     */
    static function CBAjax_payWithNet30(stdClass $args): stdClass {
        $orderID = CBModel::valueAsCBID(
            $args,
            'orderID'
        );

        if ($orderID === null) {
            throw new CBExceptionWithValue(
                'The "orderID" argument is invalid.',
                $args,
                'fc7b9389bbe17bcf1125e814d4887be6657c5613'
            );
        }

        $model = CBModels::fetchModelByIDNullable($orderID);

        if (empty($model)) {
            throw new CBException(
                "There is no order with the ID: {$orderID}",
                '',
                '479097c8f0baa8f5ccddc6eac889a649085be9d2'
            );
        }

        if (!LEWholesaleCustomerSettings::currentUserIsApprovedForNet30()) {
            throw new CBException(
                'You are not currently approved to make net 30 purchases.',
                '',
                '3a143b95203d573210746e3d17ed8229eb84bf2f'
            );
        }

        if (
            ColbyUser::getCurrentUserCBID() !==
            CBModel::valueAsID($model, 'customerHash')
        ) {
            throw new CBException(
                'You are not the user that created this order.',
                '',
                '194826019c501f17af921ced1f8d2f91c36c2fd8'
            );
        }

        if (!empty($model->orderPaymentMethod)) {
            throw new CBException(
                'This order has already been purchased.',
                '',
                '375bce5e8414df09fc9cbdf7c31185965f3badb1'
            );
        }

        $spec = CBModels::fetchSpecByIDNullable($orderID);

        CBModel::merge(
            $spec,
            (object)[
                'orderPaymentMethod' => 'Net30',
                'orderPaymentAuthorized' => time(),
                'orderPaymentAuthorizedAmountInCents' => CBModel::valueAsInt(
                    $model,
                    'orderSubtotalInCents'
                ),
            ]
        );

        CBDB::transaction(
            function () use ($spec) {
                SCOrder::updateOrdersTableRow($spec);
                CBModels::save($spec);
            }
        );

        $response = (object)[];

        try {
            $model = CBModels::fetchModelByIDNullable($orderID);

            SCOrderConfirmationEmail::send($model);

            $response->message = (
                'Your order was successfully placed. A receipt has been ' .
                'sent to your email address.'
            );
        } catch (Throwable $throwable) {
            CBErrorHandler::report($throwable);

            $shipOrderToEmail = CBModel::valueToString(
                $model,
                'shipOrderToEmail'
            );

            $response->message = (
                "Your order was successfully placed, but an error occurred " .
                "when we tried to send an email receipt to your email " .
                "address: {$shipOrderToEmail}. Please save or print this " .
                "page for a record of your order."
            );
        }

        $response->paymentWasSuccessful = true;

        return $response;
    }
    /* CBAjax_payWithNet30() */



    /**
     * @return object
     */
    static function CBAjax_payWithNet30_getUserGroupClassName() {
        return 'LEWholesaleCustomersUserGroup';
    }



    /**
     * @param object $args
     *
     *      {
     *          orderID: ID
     *      }
     *
     * @return void
     */
    static function CBAjax_sendEmail(stdClass $args): void {
        $orderID = CBModel::valueAsID($args, 'orderID');

        if (empty($orderID)) {
            throw new InvalidArgumentException('orderID');
        }

        SCOrderConfirmationEmail::send(
            CBModelCache::fetchModelByID($orderID)
        );
    }
    /* CBAjax_sendEmail() */



    /**
     * @return string
     */
    static function CBAjax_sendEmail_getUserGroupClassName(): string {
        return 'CBAdministratorsUserGroup';
    }



    /* -- CBModel interfaces ---------- */



    /**
     * @param object $spec
     *
     * @return object
     */
    static function CBModel_build(
        stdClass $spec
    ): stdClass {
        $cartItemSpecs = CBModel::valueToArray(
            $spec,
            'orderItems'
        );

        $cartItemModels = array_map(
            function ($cartItemSpec): stdClass {
                return CBModel::build(
                    $cartItemSpec
                );
            },
            $cartItemSpecs
        );


        $noteSpecs = CBModel::valueToArray(
            $spec,
            'notes'
        );

        $noteModels = array_map(
            function ($noteSpec) {
                $className = CBModel::valueToString(
                    $noteSpec,
                    'className'
                );

                if ($className !== 'CBNote') {
                    throw CBException::createModelIssueException(
                        'A note in an order does not have a valid class name.',
                        $noteSpec,
                        '81b72e04330df3bde3ece481082fa049c1129207'
                    );
                }

                return CBModel::build(
                    $noteSpec
                );
            },
            $noteSpecs
        );


        return (object)[
            'kindClassName' => CBModel::valueToString(
                $spec,
                'kindClassName'
            ),

            'notes' => $noteModels,

            'orderCreated' => CBModel::valueAsInt(
                $spec,
                'orderCreated'
            ),

            'orderCreatedYearMonth' => CBModel::valueToString(
                $spec,
                'orderCreatedYearMonth'
            ),

            'isWholesale' => CBModel::valueToBool(
                $spec,
                'isWholesale'
            ),

            'customerHash' => CBModel::valueAsID(
                $spec,
                'customerHash'
            ),

            'shipOrderToFullName' => trim(
                CBModel::valueToString(
                    $spec,
                    'shipOrderToFullName'
                )
            ),

            'shipOrderToEmail' => trim(
                CBModel::valueToString(
                    $spec,
                    'shipOrderToEmail'
                )
            ),

            'shipOrderToAddressLine1' => trim(
                CBModel::valueToString(
                    $spec,
                    'shipOrderToAddressLine1'
                )
            ),

            'shipOrderToAddressLine2' => trim(
                CBModel::valueToString(
                    $spec,
                    'shipOrderToAddressLine2'
                )
            ),

            'shipOrderToCity' => trim(
                CBModel::valueToString(
                    $spec,
                    'shipOrderToCity'
                )
            ),

            'shipOrderToStateProvinceOrRegion' => trim(
                CBModel::valueToString(
                    $spec,
                    'shipOrderToStateProvinceOrRegion'
                )
            ),

            'shipOrderToPostalCode' => trim(
                CBModel::valueToString(
                    $spec,
                    'shipOrderToPostalCode'
                )
            ),

            'shipOrderToCountryCode' => trim(
                CBModel::valueToString(
                    $spec,
                    'shipOrderToCountryCode'
                )
            ),

            'shipOrderToCountryName' => trim(
                CBModel::valueToString(
                    $spec,
                    'shipOrderToCountryName'
                )
            ),

            'shipOrderToPhone' => trim(
                CBModel::valueToString(
                    $spec,
                    'shipOrderToPhone'
                )
            ),

            'shipOrderWithSpecialInstructions' => trim(
                CBModel::valueToString(
                    $spec,
                    'shipOrderWithSpecialInstructions'
                )
            ),

            'orderItems' => $cartItemModels,

            'orderSubtotalInCents' => CBModel::valueAsInt(
                $spec,
                'orderSubtotalInCents'
            ),

            'orderShippingMethod' => CBModel::valueToString(
                $spec,
                'orderShippingMethod'
            ),

            'orderShippingChargeInCents' => CBModel::valueAsInt(
                $spec,
                'orderShippingChargeInCents'
            ),

            'orderSalesTaxInCents' => CBModel::valueAsInt(
                $spec,
                'orderSalesTaxInCents'
            ),

            'orderTotalInCents' => CBModel::valueAsInt(
                $spec,
                'orderTotalInCents'
            ),

            'orderRowId' => CBModel::valueAsInt(
                $spec,
                'orderRowId'
            ),

            'orderPaymentMethod' => CBModel::valueToString(
                $spec,
                'orderPaymentMethod'
            ),

            'orderPaymentStripeChargeId' => CBModel::valueToString(
                $spec,
                'orderPaymentStripeChargeId'
            ),

            'orderPaymentAuthorized' => CBModel::valueAsInt(
                $spec,
                'orderPaymentAuthorized'
            ),

            'orderPaymentAuthorizedAmountInCents' => CBModel::valueAsInt(
                $spec,
                'orderPaymentAuthorizedAmountInCents'
            ),

            'orderPaymentCaptured' => CBModel::valueAsInt(
                $spec,
                'orderPaymentCaptured'
            ),

            'orderPaymentCapturedAmountInCents' => CBModel::valueAsInt(
                $spec,
                'orderPaymentCapturedAmountInCents'
            ),

            'orderPaymentCapturedByUserCBID' => CBModel::valueAsCBID(
                $spec,
                'orderPaymentCapturedByUserCBID'
            ),

            'orderEmailWasSent' => CBModel::valueToBool(
                $spec,
                'orderEmailWasSent'
            ),

            'orderArchived' => CBModel::valueAsInt(
                $spec,
                'orderArchived'
            ),

            'orderArchivedByUserCBID' => CBModel::valueAsCBID(
                $spec,
                'orderArchivedByUserCBID'
            ),
        ];
    }
    /* CBModel_build() */



    /**
     * @param object $spec
     *
     * @return object
     */
    static function CBModel_upgrade(
        $orderSpec
    ): stdClass {
        $upgradedOrderSpec = CBModel::clone(
            $orderSpec
        );

        /* orderItems */

        $originalCartItemSpecs = CBModel::valueToArray(
            $upgradedOrderSpec,
            'orderItems'
        );

        $upgradedOrderSpec->orderItems = array_map(
            function ($originalCartItemSpec) {

                /**
                 * @NOTE 2019_11_29
                 *
                 *      Removed some more complex code here that should no
                 *      longer be necessary. If the original cart item spec is
                 *      not a model this will fail and needs to be looked at by
                 *      a developer.
                 */

                return CBModel::upgrade($originalCartItemSpec);
            },
            $originalCartItemSpecs
        );

        /* done */

        return $upgradedOrderSpec;
    }
    /* CBModel_upgrade() */



    /* -- functions ---------- */



    /**
     * @param [object] $cartItemModels
     *
     * @return int
     */
    static function calculateSubtotalInCents(
        array $cartItemModels
    ): int {
        return array_reduce(
            $cartItemModels,
            function ($subtotalInCents, $itemSpec) {
                return (
                    $subtotalInCents +
                    SCCartItem::getPriceInCents($itemSpec)
                );
            },
            0
        );
    }
    /* calculateSubtotalInCents() */



    /**
     * @param object $spec
     *
     *      This function sets the `orderRowId` property on the spec.
     *
     * @return void
     */
    static function createOrdersTableRow(
        stdClass $spec
    ): void {
        $archiveID = $spec->ID;
        $archiveIDAsSQL = CBID::toSQL($archiveID);
        $createdAsSQL = (int)$spec->orderCreated;
        $createdYearMonth = $spec->orderCreatedYearMonth;
        $createdYearMonthAsSQL = CBDB::stringToSQL($createdYearMonth);

        $SQL = <<<EOT

            INSERT INTO `SCOrders`
            (
                archiveId,
                created,
                createdYearMonth
            )
            VALUES
            (
                {$archiveIDAsSQL},
                {$createdAsSQL},
                {$createdYearMonthAsSQL}
            )

        EOT;

        Colby::query($SQL);

        /**
         * Save the row ID to the archive.
         */

        $orderRowId = Colby::mysqli()->insert_id;
        $spec->orderRowId = $orderRowId;

        SCOrder::updateOrdersTableRow($spec);
    }
    /* createOrdersTableRow() */



    /**
     * @param object $orderModel
     *
     * @return string
     */
    static function createOrderInformationHTML(
        stdClass $orderModel
    ): string {
        $shipOrderToFullNameHTML = cbhtml(
            $orderModel->shipOrderToFullName
        );

        $shipOrderToAddressLine1HTML = cbhtml(
            $orderModel->shipOrderToAddressLine1
        );

        $shipOrderToAddressLine2HTML = cbhtml(
            $orderModel->shipOrderToAddressLine2
        );

        $shipOrderToCityHTML = cbhtml(
            $orderModel->shipOrderToCity
        );

        $shipOrderToStateProvinceOrRegionHTML = cbhtml(
            $orderModel->shipOrderToStateProvinceOrRegion
        );

        $shipOrderToPostalCodeHTML = cbhtml(
            $orderModel->shipOrderToPostalCode
        );

        $shipOrderToCountryNameAsHTML = cbhtml(
            CBModel::valueToString($orderModel, 'shipOrderToCountryName')
        );

        $shipOrderWithSpecialInstructionsHTML = cbhtml(
            $orderModel->shipOrderWithSpecialInstructions
        );

        $shipOrderToPhoneHTML = cbhtml(
            $orderModel->shipOrderToPhone
        );

        $shipOrderToEmailHTML = cbhtml(
            $orderModel->shipOrderToEmail
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

        ob_start();

        ?>

        <dl>
            <dt><p>Shipping Address</dt>
            <dd>
                <p><?= implode("<br>", $addressLines) ?>
            </dd>
        </dl>

        <?php if ($shipOrderWithSpecialInstructionsHTML) { ?>

            <dl>
                <dt><p>Special Instructions</dt>
                <dd>
                    <p><?= $shipOrderWithSpecialInstructionsHTML ?>
                </dd>
            </dl>

        <?php } ?>

        <?php if ($shipOrderToPhoneHTML) { ?>

            <dl>
                <dt><p>Phone</dt>
                <dd>
                    <p><?= $shipOrderToPhoneHTML ?>
                </dd>
            </dl>

        <?php } ?>

        <dl>
            <dt><p>Email Address</dt>
            <dd><p><?= $shipOrderToEmailHTML ?></dd>
        </dl>

        <?php

        return ob_get_clean();
    }
        /* createOrderInformationHTML() */



    /**
     * @param object $orderModel
     *
     * @return string
     */
    static function createSummaryHTML(
        stdClass $orderModel
    ): string {
        $orderSubtotalInCents = $orderModel->orderSubtotalInCents;

        $orderSubtotalInDollars = CBConvert::centsToDollars(
            $orderSubtotalInCents
        );

        $orderShippingChargeInCents = $orderModel->orderShippingChargeInCents;

        $orderShippingChargeInDollars = CBConvert::centsToDollars(
            $orderShippingChargeInCents
        );

        $orderSalesTaxInCents = $orderModel->orderSalesTaxInCents;

        $orderSalesTaxInDollars = CBConvert::centsToDollars(
            $orderSalesTaxInCents
        );

        $orderTotalInCents = $orderModel->orderTotalInCents;

        $orderTotalInDollars = CBConvert::centsToDollars(
            $orderTotalInCents
        );

        ob_start();

        ?>

        <table class="formatted-for-order">
            <tr>
                <th style="width: 200px;">Subtotal</th>
                <td class="total"><?= $orderSubtotalInDollars; ?></td>
            </tr>
            <tr>
                <th>Shipping</th>
                <td class="total"><?= $orderShippingChargeInDollars; ?></td>
            </tr>
            <tr>
                <th>Sales Tax</th>
                <td class="total"><?= $orderSalesTaxInDollars; ?></td>
            </tr>
            <tr class="total">
                <th>Total</th>
                <td class="total"><?= $orderTotalInDollars; ?></td>
            </tr>
        </table>

        <?php

        return ob_get_clean();
    }
    /* createSummaryHTML() */



    /**
     * @param string $countryCode
     * @param string $state
     *
     *      State, province, or region.
     *
     * @return float
     */
    static function currentSalesTaxRate($countryCode, $state) {
        $isWholesaleCustomer = CBUserGroup::userIsMemberOfUserGroup(
            ColbyUser::getCurrentUserCBID(),
            'LEWholesaleCustomersUserGroup'
        );

        if ($isWholesaleCustomer) {
            return 0;
        }

        if ($countryCode === 'US' && preg_match('/^WA/i', $state)) {
            $salesTaxRate = CBConvert::valueAsNumber(
                CBSitePreferences::customValueForKey('SCSalesTaxWA')
            ) ?? 0;

            if (
                !is_numeric($salesTaxRate) ||
                $salesTaxRate < 0 ||
                $salesTaxRate > 1
            ) {
                throw new RuntimeException(
                    'The SCSalesTaxWA site preferences custom value is not'
                    . ' set to a valid value.'
                );
            } else {
                return $salesTaxRate;
            }
        } else {
            return 0;
        }
    }
    /* currentSalesTaxRate() */



    /**
     * @param object $orderModel
     *
     * @return object
     *
     *      The return object is use by the checkout page display order
     *      information to the user and report the order data to analytics.
     */
    static function orderToCheckoutInformation(
        stdClass $orderModel
    ): stdClass {
        return (object)[
            'orderRowId' => $orderModel->orderRowId,

            'orderTotalInCents' => $orderModel->orderTotalInCents,

            'orderShippingChargeInCents' => (
                $orderModel->orderShippingChargeInCents
            ),

            'orderSalesTaxInCents' => $orderModel->orderSalesTaxInCents,

            'orderItems' => CBModel::valueToArray(
                $orderModel,
                'orderItems'
            ),
        ];
    }
    /* orderToCheckoutInformation() */



    /**
     * This function takes a spec and performs actions on it to make it a valid
     * SCOrder spec. Unlike build(), which can be called repeatedly, prepare()
     * should only be called once per spec.
     *
     * This function is very similar to the SCCartItem::update() function. It is
     * under consideration to formalize this pattern and add prepare() and the
     * CBModel_prepare() interface to the CBModel class.
     *
     * The build process is resonsible for validating specs. This function is
     * not guaranteed to return a valid SCOrder spec.
     *
     * This function is responsible for:
     *
     *      Updating (preparing) the cart item specs.
     *
     *      Calculating the order subtotal.
     *
     *      Calculating the order sales tax.
     *
     *      Calculating the order shipping cost.
     *
     *      Calculating the order total.
     *
     * @param object $orderSpec
     *
     * @return object
     */
    static function prepare(
        stdClass $orderSpec
    ): stdClass {
        $preparedSpec = CBModel::clone($orderSpec);


        /* subtotal */

        $orderCartItemSpecs = CBModel::valueToArray(
            $preparedSpec,
            'orderItems'
        );

        $preparedSpec->orderSubtotalInCents = SCOrder::calculateSubtotalInCents(
            $orderCartItemSpecs
        );


        /* kindClassName */

        $orderKindClassName = SCOrder::getOrderKindClassName(
            $orderSpec
        );

        $preparedSpec->kindClassName = $orderKindClassName;


        /* orderShippingMethod */

        /**
         * @NOTE 2018_12_15
         *
         *      Eventually, if a site allows it, the shipping method may be
         *      chosen by the user with options specified by the kindClassName
         *      class.
         */

        $preparedSpec->orderShippingMethod = (
            SCOrderKind::defaultShippingMethod(
                $orderKindClassName
            )
        );


        /* orderShippingChargeInCents */

        $preparedSpec->orderShippingChargeInCents = (
            SCOrderKind::shippingChargeInCents(
                $orderKindClassName,
                $preparedSpec
            )
        );


        /* orderSalesTaxInCents */

        $preparedSpec->orderSalesTaxInCents = (
            SCOrderKind::salesTaxInCents(
                $orderKindClassName,
                $preparedSpec
            )
        );


        /* orderTotalInCents */

        $preparedSpec->orderTotalInCents = (
            $preparedSpec->orderSubtotalInCents +
            $preparedSpec->orderShippingChargeInCents +
            $preparedSpec->orderSalesTaxInCents
        );


        /* promotions */

        $promotionModels = CBModels::fetchModelsByID2(
            SCPromotionsTable::fetchActivePromotionCBIDs()
        );

        foreach ($promotionModels as $promotionModel) {
            $preparedSpec = SCPromotion::apply(
                $promotionModel,
                $preparedSpec
            );
        }


        /* done */

        return $preparedSpec;
    }
    /* prepare() */



    /**
     * This function gets the order kind class name from an order spec. If there
     * is no order kind class name set on the spec, it returns the default order
     * kind class name.
     *
     * @param object $orderSpec
     *
     * @return string
     */
    private static function getOrderKindClassName(
        stdClass $orderSpec
    ): string {
        $orderKindClassName = CBModel::valueAsName(
            $orderSpec,
            'kindClassName'
        );

        if ($orderKindClassName === null) {
            $orderKindClassName = CBModel::valueAsName(
                CBModelCache::fetchModelByID(
                    SCPreferences::ID()
                ),
                'defaultOrderKindClassName'
            );

            if ($orderKindClassName === null) {
                throw new CBExceptionWithValue(
                    CBConvert::stringToCleanLine(<<<EOT

                        The original SCOrder spec has an invalid "kindClassName"
                        property value and this website has no default order
                        kind class name.

                    EOT),
                    $orderSpec,
                    '922fc2133e1d4f1a8b58346658cfa58cf4950104'
                );
            }
        }

        return $orderKindClassName;
    }
    /* getOrderKindClassName() */



    /**
     * @param object $spec
     *
     * @return void
     */
    static function updateOrdersTableRow(
        stdClass $spec
    ): void {
        $model = CBModel::build($spec);
        $IDAsSQL = CBID::toSQL($model->ID);

        /* is authorized */

        $isAuthorized = CBModel::valueAsInt(
            $model,
            'orderPaymentAuthorized'
        ) !== null;

        $isAuthorizedAsSQL = $isAuthorized ? "b'1'" : "b'0'";

        /* SQL */

        $SQL = <<<EOT

            UPDATE  SCOrders

            SET     isAuthorized = {$isAuthorizedAsSQL}

            WHERE   archiveId = {$IDAsSQL}

        EOT;

        Colby::query($SQL);
    }
    /* updateOrdersTableRow() */

}

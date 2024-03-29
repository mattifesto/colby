<?php

final class
SCOrder
{
    // -- CBAjax interfaces



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
    static function
    CBAjax_addNote(
        stdClass $args
    ): stdClass
    {
        $orderID =
        CBModel::valueAsID(
            $args,
            'orderID'
        );

        if (
            $orderID ===
            null
        ) {
            throw new Exception(
                'No order ID was provided.'
            );
        }

        $text =
        CBModel::valueToString(
            $args,
            'text'
        );

        if (
            trim($text) ===
            ''
        ) {
            throw new Exception(
                'The text in the note is empty.'
            );
        }

        $noteSpec =
        (object)
        [
            'className' =>
            'CBNote',

            'text' =>
            $text,

            'timestamp' =>
            time(),

            'userID' =>
            ColbyUser::getCurrentUserCBID(),
        ];

        $updater =
        CBModelUpdater::fetch(
            (object)[
                'ID' =>
                $orderID,
            ]
        );

        if (
            $updater->working->className !==
            'SCOrder'
        ) {
            throw new CBExceptionWithValue(
                'The target model is not an order.',
                $updater->working,
                '121538e14d966020ca42d71b69d6771e25fbde1a'
            );
        }

        $notes =
        CBModel::valueToArray(
            $updater->working,
            'notes'
        );

        array_push(
            $notes,
            $noteSpec
        );

        $updater->working->notes =
        $notes;

        CBModelUpdater::save(
            $updater
        );

        $noteModel =
        CBModel::build(
            $noteSpec
        );

        return $noteModel;
    }
    // CBAjax_addNote()



    /**
     * @return string
     */
    static function
    CBAjax_addNote_getUserGroupClassName(
    ): string
    {
        return 'CBAdministratorsUserGroup';
    }
    // CBAjax_addNote_getUserGroupClassName()



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
    static function
    CBAjax_charge(
        stdClass $args
    ): stdClass {
        $orderID = CBModel::valueAsID(
            $args,
            'orderID'
        );

        $stripeToken = CBModel::valueToString(
            $args,
            'stripeToken'
        );

        $response = (object)[];

        $model = CBModels::fetchModelByCBID(
            $orderID
        );

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

        $orderTotalInCents = SCOrder::getTotalInCents(
            $model,
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

        /* generate order ID */

        $orderID = CBID::generateRandomCBID();

        /* create spec */

        $spec = (object)[
            'ID' => $orderID,
            'className' => 'SCOrder',
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
         * order kind class name
         *
         * Since this function generates an order spec, it uses the default
         * order kind class name every time. In the future it is likely that a
         * spec will be delivered to this function with its order kind class
         * name property alreadt set.
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


        SCOrder::setKindClassName(
            $spec,
            $orderKindClassName
        );



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
         * SCOrderKind::liveOrderIsWholesale() is only called for live orders.
         * For other orders, the code will set the property value to whatever
         * the code wants it to be.
         */
        SCOrder::setIsWholesale(
            $spec,
            SCOrderKind::liveOrderIsWholesale(
                $spec
            )
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

        $spec->orderItems = SCShoppingCart::getItems(
            CBModel::valueToObject(
                $args,
                'shoppingCart'
            )
        );


        /**
         * One by one, the above actions that are suitable to be done in
         * prepare() will be migrated out of this function and into prepare().
         */

        $preparedSpec = SCOrder::prepare(
            $spec
        );


        /**
         * If prepare() has produced an order with issues make this Ajax
         * function call fail.
         */

        foreach ($preparedSpec->orderItems as $updatedCartItemSpec) {
            $quantity = SCCartItem::getQuantity(
                $updatedCartItemSpec
            );

            $isNotAvailable = SCCartItem::getIsNotAvailable(
                $updatedCartItemSpec
            );

            if ($quantity === 0 || $isNotAvailable) {
                $message = CBConvert::stringToCleanLine(<<<EOT

                    There is an issue with one of the items in your shopping
                    cart that must be reviewed on the shopping cart page before
                    your order can be completed.

                EOT);

                return (object)[
                    'wasCancelled' => true,
                    'messageAsText' => $message,
                ];
            }
            /* if */
        }
        /* foreach */


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

        $subtotalInCents = SCOrder::getSubtotalInCents(
            $preparedSpec
        );

        if ($subtotalInCents < $minimumSubtotalInCents) {
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

            $response->SCOrder_create_orderSummaryCBMessage = (
                SCOrder::toSummaryCBMesssage(
                    $orderModel
                )
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
    static function
    CBAjax_payWithNet30(
        stdClass $args
    ): stdClass {
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

        $model = CBModels::fetchModelByCBID(
            $orderID
        );

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

        $spec = CBModels::fetchSpecByID(
            $orderID
        );

        $orderTotalInCents = SCOrder::getTotalInCents(
            $model,
        );

        CBModel::merge(
            $spec,
            (object)[
                'orderPaymentMethod' => 'Net30',
                'orderPaymentAuthorized' => time(),
                'orderPaymentAuthorizedAmountInCents' => $orderTotalInCents,
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
     * @NOTE 2020_11_21
     *
     *      This function does almost no verification of values. It will build a
     *      model that doesn't have any values at all. It's time to very
     *      carefully start requiring certain values to be set in a way that
     *      doesn't risk causing error for existing order models.
     *
     * @param object $spec
     *
     * @return object
     */
    static function
    CBModel_build(
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

        $subtotalInCents = SCOrder::getSubtotalInCents(
            $spec
        );

        if (
            $subtotalInCents === null ||
            $subtotalInCents < 0
        ) {
            throw new CBExceptionWithValue(
                CBConvert::stringToCleanLine(<<<EOT

                    The subtotal for this SCOrder spec is either unset or less
                    than zero.

                EOT),
                $spec,
                '3a02bab165d07b85ffa5fbdebf72612dede4a524'
            );
        }

        $discountInCents = SCOrder::getDiscountInCents(
            $spec
        );

        if (
            $discountInCents < 0
        ) {
            throw new CBExceptionWithValue(
                CBConvert::stringToCleanLine(<<<EOT

                    The discount for this SCOrder spec less than zero.

                EOT),
                $spec,
                '4879dfa5d33619796aa081a7d41499b729a79318'
            );
        }

        $model = (object)[
            'notes' => $noteModels,

            'orderCreated' => CBModel::valueAsInt(
                $spec,
                'orderCreated'
            ),

            'orderCreatedYearMonth' => CBModel::valueToString(
                $spec,
                'orderCreatedYearMonth'
            ),

            'isWholesale' => SCOrder::getIsWholesale(
                $spec
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

            'orderSubtotalInCents' => $subtotalInCents,

            'SCOrder_discountInCents' => $discountInCents,

            'orderShippingMethod' => CBModel::valueToString(
                $spec,
                'orderShippingMethod'
            ),

            'orderShippingChargeInCents' => SCOrder::getShippingChargeInCents(
                $spec
            ),

            'orderSalesTaxInCents' => SCOrder::getSalesTaxInCents(
                $spec
            ),

            'orderTotalInCents' => SCOrder::getTotalInCents(
                $spec
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


        /**
         * @NOTE 2021_03_19
         *
         *      For historical reasons the order kind class name is allowed to
         *      be unset.
         */
        SCOrder::setKindClassName(
            $model,
            SCOrder::getKindClassName(
                $spec
            )
        );


        return $model;
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



    /* -- accessors -- */



    /**
     * @see documentation
     *
     * @param object $orderModel
     *
     * @return int
     */
    static function
    getDiscountInCents(
        stdClass $orderModel
    ): int {
        return CBModel::valueAsInt(
            $orderModel,
            'SCOrder_discountInCents'
        ) ?? 0;
    }
    /* getDiscountInCents() */



    /**
     * @see documentation
     *
     * @param object $orderSpec
     * @param int $discountInCents
     *
     * @return void
     */
    static function
    setDiscountInCents(
        stdClass $orderSpec,
        int $discountInCents
    ): void {
        $orderSpec->SCOrder_discountInCents = $discountInCents;
    }
    /* setDiscountInCents() */



    /**
     * @param object $orderModel
     *
     * @return bool
     */
    static function
    getIsWholesale(
        stdClass $orderModel
    ): bool {
        return CBModel::valueToBool(
            $orderModel,
            'isWholesale'
        );
    }
    /* getIsWholesale() */



    /**
     * @param object $orderSpec
     * @param bool $isWholesale
     *
     * @return void
     */
    static function
    setIsWholesale(
        stdClass $orderSpec,
        bool $isWholesale
    ): void {
        $orderSpec->isWholesale = $isWholesale;
    }
    /* setIsWholesale() */



    /**
     * @see documentation
     *
     * @param object $orderModel
     *
     * @return string
     */
    static function
    getKindClassName(
        stdClass $orderModel
    ): string {
        return CBModel::valueAsName(
            $orderModel,
            'kindClassName'
        ) ?? '';
    }
    /* getKindClassName() */



    /**
     * @see documentation
     *
     * @param object $orderSpec
     * @param string $kindClassName
     *
     * @return void
     */
    static function
    setKindClassName(
        stdClass $orderSpec,
        string $kindClassName
    ): void {
        $orderSpec->kindClassName = $kindClassName;
    }
    /* setKindClassName() */



    /**
     * @param object $orderModel
     *
     * @return int
     */
    static function
    getSalesTaxInCents(
        stdClass $orderModel
    ): int {
        return CBModel::valueAsInt(
            $orderModel,
            'orderSalesTaxInCents'
        ) ?? 0;
    }
    /* getSalesTaxInCents() */



    /**
     * @param object $orderModel
     *
     * @return string
     */
    static function
    getShippingAddressFullName(
        stdClass $orderModel
    ): string {
        return CBModel::valueToString(
            $orderModel,
            'shipOrderToFullName'
        );
    }
    /* getShippingAddressFullName() */



    /**
     * @param object $orderModel
     *
     * @return int
     */
    static function
    getShippingChargeInCents(
        stdClass $orderModel
    ): int {
        return CBModel::valueAsInt(
            $orderModel,
            'orderShippingChargeInCents'
        ) ?? 0;
    }
    /* getShippingChargeInCents() */



    /**
     * @see documentation
     *
     * @param object $orderModel
     *
     * @return int|null
     */
    static function
    getSubtotalInCents(
        stdClass $orderModel
    ): ?int {
        return CBModel::valueAsInt(
            $orderModel,
            'orderSubtotalInCents'
        );
    }
    /* getSubtotalInCents() */



    /**
     * @see documentation
     *
     * @param object $orderSpec
     * @param int $subtotalInCents
     *
     * @return void
     */
    static function
    setSubtotalInCents(
        stdClass $orderSpec,
        int $subtotalInCents
    ): void {
        $orderSpec->orderSubtotalInCents = $subtotalInCents;
    }
    /* setSubtotalInCents() */



    /**
     * @param object $orderModel
     *
     * @return int|null
     */
    static function
    getTotalInCents(
        stdClass $orderModel
    ): ?int {
        return CBModel::valueAsInt(
            $orderModel,
            'orderTotalInCents'
        );
    }
    /* getTotalInCents() */



    /**
     * @param object $orderSpec
     * @param int $totalInCents
     *
     * @return void
     */
    static function
    setTotalInCents(
        stdClass $orderSpec,
        int $totalInCents
    ): void {
        $orderSpec->orderTotalInCents = $totalInCents;
    }
    /* setTotalInCents() */



    /* -- functions ---------- */



    /**
     * @param [object] $cartItemModels
     *
     * @return int
     */
    static function
    calculateSubtotalInCents(
        array $cartItemModels
    ): int {
        return array_reduce(
            $cartItemModels,
            function ($subtotalInCents, $itemSpec) {
                return (
                    $subtotalInCents +
                    SCCartItem::getSubtotalInCents(
                        $itemSpec
                    )
                );
            },
            0
        );
    }
    /* calculateSubtotalInCents() */



    /**
     * @param object $orderModel
     *
     * @return int
     */
    static function
    calculateTaxableAmountInCents(
        stdClass $orderModel
    ): int {
        $subtotalInCents = SCOrder::getSubtotalInCents(
            $orderModel
        ) ?? 0;

        $discountInCents = SCOrder::getDiscountInCents(
            $orderModel
        );

        $shippingChargeInCents = SCOrder::getShippingChargeInCents(
            $orderModel
        );

        return max(
            $subtotalInCents - $discountInCents + $shippingChargeInCents,
            0
        );
    }
    /* calculateTaxableAmountInCents() */



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
     * @deprecated 2021_03_04
     *
     *      Use SCOrder::toSummaryCBMesssage().
     *
     * @param object $orderModel
     *
     * @return string
     */
    static function
    createSummaryHTML(
        stdClass $orderModel
    ): string {
        $subtotalInCents = SCOrder::getSubtotalInCents(
            $orderModel
        );

        $orderSubtotalInDollars = CBConvert::centsToDollars(
            $subtotalInCents
        );

        $discountInCents = SCOrder::getDiscountInCents(
            $orderModel
        );

        $discountInDollars = CBConvert::centsToDollars(
            $discountInCents
        );

        $orderShippingChargeInCents = $orderModel->orderShippingChargeInCents;

        $orderShippingChargeInDollars = CBConvert::centsToDollars(
            $orderShippingChargeInCents
        );

        $orderSalesTaxInCents = $orderModel->orderSalesTaxInCents;

        $orderSalesTaxInDollars = CBConvert::centsToDollars(
            $orderSalesTaxInCents
        );

        $orderTotalInCents = SCOrder::getTotalInCents(
            $orderModel
        );

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

            <?php if ($discountInCents > 0) { ?>

                <tr>
                    <th>Discount</th>
                    <td class="total"><?= $discountInDollars ?></td>
                </tr>

            <?php } ?>

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
    static function
    orderToCheckoutInformation(
        stdClass $orderModel
    ): stdClass {
        return (object)[
            'orderRowId' => $orderModel->orderRowId,

            'orderTotalInCents' => SCOrder::getTotalInCents(
                $orderModel
            ),

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
     * @see documentation
     *
     * @param object $orderSpec
     *
     * @return object
     */
    static function
    prepare(
        stdClass $originalOrderSpec
    ): stdClass {
        $preparedOrderSpec = CBModel::clone(
            $originalOrderSpec
        );


        /* cart items */

        $originalCartItemSpecs = CBModel::valueToArray(
            $preparedOrderSpec,
            'orderItems'
        );

        /**
         * @TODO 2020_05_31
         *
         *      This should use CBModel::prepare() on each cart item spec in the
         *      future.
         */

        $preparedOrderSpec->orderItems = SCCartItem::updateSpecs(
            $originalCartItemSpecs
        );


        /* subtotal */

        $orderCartItemSpecs = CBModel::valueToArray(
            $preparedOrderSpec,
            'orderItems'
        );

        $subtotalInCents = SCOrder::calculateSubtotalInCents(
            $orderCartItemSpecs
        );

        SCOrder::setSubtotalInCents(
            $preparedOrderSpec,
            $subtotalInCents
        );


        /* order kind class name */

        SCOrder::setKindClassName(
            $preparedOrderSpec,
            SCOrder::prepareOrderKindClassName(
                $preparedOrderSpec
            )
        );


        /* orderShippingMethod */

        /**
         * @NOTE 2018_12_15
         *
         *      Eventually, if a site allows it, the shipping method may be
         *      chosen by the user with options specified by the order kind
         *      class.
         */

        $preparedOrderSpec->orderShippingMethod = (
            SCOrderKind::defaultShippingMethod(
                SCOrder::getKindClassName(
                    $preparedOrderSpec
                )
            )
        );


        /* promotions */

        $promotionModels = (
            SCPromotionsTable::fetchCachedActivePromotionModels()
        );

        foreach ($promotionModels as $promotionModel) {
            $preparedOrderSpec = SCPromotion::apply(
                $promotionModel,
                $preparedOrderSpec
            );
        }


        /* orderShippingChargeInCents */

        $preparedOrderSpec->orderShippingChargeInCents = (
            SCOrderKind::shippingChargeInCents(
                SCOrder::getKindClassName(
                    $preparedOrderSpec
                ),
                $preparedOrderSpec
            )
        );


        /* orderSalesTaxInCents */

        $preparedOrderSpec->orderSalesTaxInCents = (
            SCOrderKind::salesTaxInCents(
                SCOrder::getKindClassName(
                    $preparedOrderSpec
                ),
                $preparedOrderSpec
            )
        );


        /* order total */

        $discountInCents = SCOrder::getDiscountInCents(
            $preparedOrderSpec
        );

        $adjustedSubtotalInCents = max(
            $subtotalInCents - $discountInCents,
            0
        );

        $totalInCents = (
            $adjustedSubtotalInCents +
            $preparedOrderSpec->orderShippingChargeInCents +
            $preparedOrderSpec->orderSalesTaxInCents
        );

        SCOrder::setTotalInCents(
            $preparedOrderSpec,
            $totalInCents
        );


        /* done */

        return $preparedOrderSpec;
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
    private static function
    prepareOrderKindClassName(
        stdClass $orderSpec
    ): string {
        $orderKindClassName = SCOrder::getKindClassName(
            $orderSpec
        );

        if ($orderKindClassName === '') {
            $orderKindClassName = CBModel::valueAsName(
                CBModelCache::fetchModelByID(
                    SCPreferences::ID()
                ),
                'defaultOrderKindClassName'
            );

            if ($orderKindClassName === null) {
                throw new CBExceptionWithValue(
                    CBConvert::stringToCleanLine(<<<EOT

                        The original SCOrder spec has an invalid order kind
                        class name and this website has no default order kind
                        class name.

                    EOT),
                    $orderSpec,
                    '922fc2133e1d4f1a8b58346658cfa58cf4950104'
                );
            }
        }

        return $orderKindClassName;
    }
    /* prepareOrderKindClassName() */



    /**
     * @param object $orderModel
     *
     * @return string
     */
    static function
    toSummaryCBMesssage(
        stdClass $orderModel
    ): string {
        $cbmessage = '';

        $subtotalInCents = SCOrder::getSubtotalInCents(
            $orderModel
        );

        $subtotalInDollars = CBConvert::centsToDollars(
            $subtotalInCents
        );

        $discountInCents = SCOrder::getDiscountInCents(
            $orderModel
        );

        $shippingChargeInCents = SCOrder::getShippingChargeInCents(
            $orderModel
        );

        $shippingChargeInDollars = CBConvert::centsToDollars(
            $shippingChargeInCents
        );

        $salesTaxInCents = SCOrder::getSalesTaxInCents(
            $orderModel
        );

        $salesTaxInDollars = CBConvert::centsToDollars(
            $salesTaxInCents
        );

        $totalInCents = SCOrder::getTotalInCents(
            $orderModel
        );

        $totalInDollars = CBConvert::centsToDollars(
            $totalInCents
        );

        $cbmessage .= <<<EOT

            --- CBUI_sectionContainer
                --- CBUI_section
                    --- CBUI_container_leftAndRight
                        --- CBUI_textColor2
                            Subtotal
                        ---
                        --- value
                            $subtotalInDollars
                        ---
                    ---

        EOT;

        if ($discountInCents > 0) {
            $discountInDollars = CBConvert::centsToDollars(
                $discountInCents
            );

            $adjustedSubtotalInCents = $subtotalInCents - $discountInCents;

            $adjustedSubtotalInDollars = CBConvert::centsToDollars(
                $adjustedSubtotalInCents
            );

            $cbmessage .= <<<EOT

                --- CBUI_container_leftAndRight
                    --- CBUI_textColor2
                        Discount
                    ---
                    --- value
                        - {$discountInDollars}
                    ---
                ---
                --- CBUI_container_leftAndRight
                    --- CBUI_textColor2
                        Adjusted Subtotal
                    ---
                    --- value
                        {$adjustedSubtotalInDollars}
                    ---
                ---

            EOT;
        }

        $cbmessage .= <<<EOT

                    --- CBUI_container_leftAndRight
                        --- CBUI_textColor2
                            Shipping
                        ---
                        --- value
                            + {$shippingChargeInDollars}
                        ---
                    ---
                    --- CBUI_container_leftAndRight
                        --- CBUI_textColor2
                            Sales Tax
                        ---
                        --- value
                            + {$salesTaxInDollars}
                        ---
                    ---
                    --- CBUI_container_leftAndRight
                        --- CBUI_textColor2
                            Total
                        ---
                        --- value
                            {$totalInDollars}
                        ---
                    ---
                ---
            ---

        EOT;

        return $cbmessage;
    }
    /* toSummaryCBMesssage() */



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

<?php

/**
 * Cart items are how purchased products are represented. A cart item represents
 * a specific item and a quantity of that item. The default quantity of an item
 * is 0. The cart items in a user's "cart" or "bag" are represented stoed in an
 * array.
 */
final class SCCartItem {

    /**
     * This variable may be set to false by tests that are purposely creating
     * error situations and want to suppress the reporting of exceptions.
     */
    static $reportUpdateExceptions = true;



    /* -- CBAjax interfaces -- -- -- -- -- */



    /**
     * @param object $args
     *
     *      {
     *          originalCartItemSpecs: [object|null]
     *      }
     *
     * @return [object|null]
     */
    static function CBAjax_updateSpecs($args): array {
        return SCCartItem::updateSpecs(
            CBModel::valueToArray($args, 'originalCartItemSpecs')
        );
    }
    /* CBAjax_updateSpecs() */



    /**
     * @return string
     */
    static function CBAjax_updateSpecs_getUserGroupClassName(): string {
        return 'CBPublicUserGroup';
    }



    /* -- CBHTMLOutput interfaces -- -- -- -- -- */



    /**
     * @return [string]
     */
    static function CBHTMLOutput_JavaScriptURLs(): array {
        return [
            Colby::flexpath(__CLASS__, 'v140.js', scliburl()),
        ];
    }



    /**
     * @return [string]
     */
    static function CBHTMLOutput_requiredClassNames(): array {
        return [
            'CBConvert',
            'CBException',
            'CBModel',
            'CBView',
            'Colby',
        ];
    }
    /* CBHTMLOutput_requiredClassNames() */



    /* -- functions -- -- -- -- -- */



    /**
     * @TODO 2019_05_15
     *
     *      There has been a ton of discussion about this function in the last
     *      week. This note is here because we have not made any changes to this
     *      function yet and at this point, even the existance of this function
     *      is in question.
     *
     * This function will sum the quantities of product item specs that
     * represent the same product into the first cart item spec for that product
     * in the array. Later cart item specs in the array for that product will be
     * set to null.
     *
     * @param [object|null] $originalCartItemSpecs
     *
     * @return [object|null]
     */
    static function consolidateSpecs(array $originalCartItemSpecs): array {
        $consolidatedCartItemSpecs = [];
        $count = count($originalCartItemSpecs);

        for (
            $currentCartItemSpecIndex = 0;
            $currentCartItemSpecIndex < $count;
            $currentCartItemSpecIndex += 1
        ) {
            $currentCartItemSpec = $originalCartItemSpecs[
                $currentCartItemSpecIndex
            ];

            $consolidatedCartItemSpec = cb_array_find(
                $consolidatedCartItemSpecs,
                function ($consolidatedCartItemSpec)
                use ($currentCartItemSpec) {
                    return SCCartItem::specsRepresentTheSameProduct(
                        $consolidatedCartItemSpec,
                        $currentCartItemSpec
                    );
                }
            );

            if ($consolidatedCartItemSpec === null) {
                array_push(
                    $consolidatedCartItemSpecs,
                    $currentCartItemSpec
                );
            } else {
                SCCartItem::setQuantity(
                    $consolidatedCartItemSpec,
                    SCCartItem::getQuantity($consolidatedCartItemSpec) +
                    SCCartItem::getQuantity($currentCartItemSpec)
                );

                array_push(
                    $consolidatedCartItemSpecs,
                    null
                );
            }
        }

        return $consolidatedCartItemSpecs;
    }
    /* consolidateSpecs() */



    /**
     * Cart items that are not available return true from this function because
     * they are either invalid cart items or the are just currently not
     * available because they are out of stock.
     *
     * Items that are not available may have quantities greater than zero and
     * prices.
     *
     * @param object $cartItemModel
     *
     *      This parameter has been of the type "mixed" in the past so that this
     *      function can return true for non-model parameters. However,
     *      non-model cart items should be either removed without notice to the
     *      customer or converted to objects when they are found.
     *
     * @return bool
     */
    static function getIsNotAvailable(
        stdClass $cartItemModel
    ): bool {
        $preferencesModel = CBModelCache::fetchModelByID(
            SCPreferences::getModelCBID()
        );

        $cartItemClassNames = CBModel::valueToArray(
            $preferencesModel,
            'cartItemClassNames'
        );

        $className = CBModel::valueToString(
            $cartItemModel,
            'className'
        );

        $cartItemIsInstalled = in_array(
            $className,
            $cartItemClassNames
        );

        if (empty($cartItemIsInstalled)) {
            return true;
        }

        $functionName = "{$className}::SCCartItem_getIsNotAvailable";

        if (is_callable($functionName)) {
            return call_user_func(
                $functionName,
                $cartItemModel
            );
        } else {
            return CBModel::valueToBool(
                $cartItemModel,
                'isNotAvailable'
            );
        }
    }
    /* getIsNotAvailable() */



    /**
     * This function returns the maximum quantity of an item that a customer is
     * allowed to having in their cart. There are many different reasons for a
     * cart item to have a maximum quantity.
     *
     * Examples:
     *
     *      For an item available at any quantity: null
     *
     *      For a unique item: 1
     *
     *      For an out of stock item: 0
     *
     * @param object $cartItemModel
     *
     * @return int|null
     *
     *      If the SCCartItem_getMaximumQuantity() interface is not implement
     *      this function returns null.
     */
    static function getMaximumQuantity(
        stdClass $cartItemModel
    ): ?float {
        $className = CBModel::valueAsName(
            $cartItemModel,
            'className'
        );

        $functionName = "{$className}::SCCartItem_getMaximumQuantity";

        if (is_callable($functionName)) {
            return call_user_func(
                $functionName,
                $cartItemModel
            );
        } else {
            return CBModel::valueAsInt(
                $cartItemModel,
                'maximumQuantity'
            );
        }
    }
    /* getMaximumQuantity() */



    /**
     * @TODO 2020_06_06
     *
     *      Rename to getSubtotalInCents(). That is, add getSubtotalInCents()
     *      and deprecate this function.
     *
     *      Then add getUnitPriceInCents(), getOriginalUnitPriceInCents(), and
     *      getOriginalSubtotalInCents().
     *
     * @param object $model
     *
     * @return int
     */
    static function getPriceInCents(
        stdClass $cartItemModel
    ): int {
        $className = CBModel::valueToString(
            $cartItemModel,
            'className'
        );

        $functionName = "{$className}::SCCartItem_getPriceInCents";

        if (is_callable($functionName)) {
            return call_user_func(
                $functionName,
                $cartItemModel
            );
        } else {
            return CBModel::valueAsInt(
                $cartItemModel,
                'priceInCents'
            ) ?? 0;
        }
    }
    /* getPriceInCents() */



    /**
     * @param mixed $cartItemModel
     *
     *      This may a a spec or a model. This parameter is mixed because it's
     *      theoretically possible for a non-object to be added to the array of
     *      cart items on the client side.
     *
     * @return number
     *
     *      The default implementation assumes that the quantity is an integer
     *      in the quantity property. If the quantity property is undefined or
     *      is not an integer, this function returns 0.
     */
    static function getQuantity($cartItemModel): float {
        $className = CBModel::valueToString($cartItemModel, 'className');
        $functionName = "{$className}::SCCartItem_getQuantity";

        if (is_callable($functionName)) {
            return call_user_func($functionName, $cartItemModel);
        } else {
            $quantity = CBModel::valueAsInt($cartItemModel, 'quantity');

            if ($quantity === null || $quantity < 0) {
                return 0;
            } else {
                return $quantity;
            }
        }
    }



    /**
     * The source URL is a root-relative URL that links to the page that most
     * represents the cart item. Some cart items will not have a source URL.
     *
     * If the cart item is a specific product, the source URL will often lead to
     * the product page, if the product has its own page. If not, it will be a
     * link the page that best represents the cart item, the page where the user
     * most likely added the cart item to their cart, or in some situations,
     * will be empty because there is no page that acts as any sort of source or
     * reference to the cart item.
     *
     * @param object $cartItemModel
     *
     * @return string
     *
     *      If a source URL is available it will be returned. If a source URL is
     *      not available an empty string will be returned.
     *
     *      Example Return Value:
     *
     *          /inventory/trucks/f150/
     *
     *      Because this function should always return a root relative URL such
     *      as the one above. The caller of this function needs to prepend
     *      cbsiteurl() if the URL will be used outside the website, for example
     *      in an email.
     */
    static function getSourceURL(stdClass $cartItemModel): string {
        $className = CBModel::valueToString($cartItemModel, 'className');
        $functionName = "{$className}::SCCartItem_getSourceURL";

        if (is_callable($functionName)) {
            return call_user_func($functionName, $cartItemModel);
        } else {
            return CBModel::valueToString($cartItemModel, 'sourceURL');
        }
    }



    /**
     * @param object $cartItemModel
     *
     * @return string
     */
    static function getTitle(stdClass $cartItemModel): string {
        $className = CBModel::valueToString($cartItemModel, 'className');
        $functionName = "{$className}::SCCartItem_getTitle";

        if (is_callable($functionName)) {
            return call_user_func($functionName, $cartItemModel);
        } else {
            return CBModel::valueToString($cartItemModel, 'title');
        }
    }



    /**
     * @param object $cartItemSpec
     * @param number $quantity
     *
     * @return void
     */
    static function setQuantity(
        stdClass $cartItemSpec,
        float $quantity
    ): void {
        $className = CBModel::valueToString($cartItemSpec, 'className');
        $functionName = "{$className}::SCCartItem_setQuantity";

        if (is_callable($functionName)) {
            call_user_func(
                $functionName,
                $cartItemSpec,
                $quantity
            );
        } else {
            $quantityAsInt = CBConvert::valueAsInt($quantity);

            if ($quantityAsInt === null || $quantityAsInt < 0) {
                throw new InvalidArgumentException(
                    'The "quantity" parameter must be an integer 0 or greater.'
                );
            }

            $cartItemSpec->quantity = $quantityAsInt;
        }
    }



    /**
     * @return bool
     */
    static function specsRepresentTheSameProduct(
        $cartItemModelA,
        $cartItemModelB
    ): bool {
        $classNameA = CBModel::valueToString(
            $cartItemModelA,
            'className'
        );

        $functionName = (
            "{$classNameA}::SCCartItem_specsRepresentTheSameProduct"
        );

        if (is_callable($functionName)) {
            $classNameB = CBModel::valueToString(
                $cartItemModelB,
                'className'
            );

            if ($classNameA === $classNameB) {
                return call_user_func(
                    $functionName,
                    $cartItemModelA,
                    $cartItemModelB
                );
            } else {
                return false;
            }
        } else {
            return false;
        }
    }
    /* specsRepresentTheSameProduct() */



    /**
     * This function renders the cart item as simple HTML that requires no
     * additional style sheets. The returned value can be used in situations
     * where full control is not available, such as in the order email sent to
     * the customer.
     *
     * @param object $cartItemModel
     *
     * @return string
     */
    static function toHTML(
        stdClass $cartItemModel
    ): string {
        $classNameA = CBModel::valueToString(
            $cartItemModel,
            'className'
        );

        $functionName = "{$classNameA}::SCCartItem_toHTML";

        if (is_callable($functionName)) {
            return call_user_func(
                $functionName,
                $cartItemModel
            );
        } else {
            $message = '';

            $titleAsMessage = CBMessageMarkup::stringToMessage(
                SCCartItem::getTitle($cartItemModel)
            );

            $sourceURLAsMessage = CBMessageMarkup::stringToMessage(
                cbsiteurl() .
                SCCartItem::getSourceURL($cartItemModel)
            );

            $quantityAndSubtotalAsMessage = implode(
                '((br))',
                [
                    (
                        "Quantity: " .
                        SCCartItem::getQuantity($cartItemModel)
                    ),
                    (
                        "Subtotal: $" .
                        CBConvert::centsToDollars(
                            SCCartItem::getPriceInCents($cartItemModel)
                        )
                    ),
                ]
            );

            $cartItemMessage = CBModel::valueToString(
                $cartItemModel,
                'message'
            );


            $message = <<<EOT

                ({$titleAsMessage} (a {$sourceURLAsMessage}))

                {$quantityAndSubtotalAsMessage}

                {$cartItemMessage}

            EOT;

            return  CBMessageMarkup::messageToHTML($message);
        }
    }
    /* toHTML() */



    /**
     * This function is used to render the cart item for use in text only
     * situations, most notably the text version of the order email sent to the
     * customer.
     *
     * @param object $cartItemModel
     *
     * @return string
     */
    static function toText($cartItemModel): string {
        $classNameA = CBModel::valueToString($cartItemModel, 'className');
        $functionName = "{$classNameA}::SCCartItem_toText";

        if (is_callable($functionName)) {
            return call_user_func($functionName, $cartItemModel);
        } else {
            ob_start();

            try {
                echo SCCartItem::getTitle($cartItemModel),
                    "\n";

                echo 'Quantity: ',
                    SCCartItem::getQuantity($cartItemModel),
                    "\n";

                echo 'Subtotal: $',
                    CBConvert::centsToDollars(
                        SCCartItem::getPriceInCents($cartItemModel)
                    ),
                    "\n\n";


                $message = CBModel::valueToString($cartItemModel, 'message');

                if ($message !== '') {
                    echo CBMessageMarkup::messageToText($message);
                }

                return ob_get_clean();
            } catch (Throwable $throwable) {
                ob_end_clean();
                throw $throwable;
            }
        }
    }



    /**
     * This function updates a cart item in the following ways:
     *
     *      Provide the most recent price, title, message, and other product
     *      data.
     *
     *      Handle product specific promotions such as price discounts.
     *
     *      Enforce minimum or maximum quantity contraints.
     *
     *      Remove unknown properties. This also prevents clients from adding
     *      huge chucks of data to cart items causing database growth for no
     *      reason.
     *
     * @param mixed $originalCartItemSpec
     *
     *      This parameter is mixed because it's possible for a non-object to be
     *      added to the array of cart items on the client.
     *
     * @return object|null
     *
     *      cart item that is not a model -> null
     *
     *      unrecognized cart item ->
     *          original cart item that is removable
     *          with a message explaining that it is not recognized
     *
     *      cart item that causes an exception ->
     *          original cart item that is removable
     *          with a message explaining that this item caused an error
     *          and specific error information if the user is a developer
     *
     *      cart item that is out of stock or currently unavailable ->
     *          updated item that is removable
     *          with a message explaining why it is unavailable
     *
     *      cart item with no issues ->
     *          updated cart item
     *          with a message sharing more cart item details and features
     */
    static function update(
        $originalCartItemSpec
    ): ?stdClass {
        if (CBConvert::valueAsModel($originalCartItemSpec) === null) {
            return null;
        }

        try {
            $originalCartItemSpecClassName = CBModel::valueToString(
                $originalCartItemSpec,
                'className'
            );

            $originalCartItemSpecID = CBModel::valueAsID(
                $originalCartItemSpec,
                'ID'
            );

            $functionName = (
                "{$originalCartItemSpecClassName}::SCCartItem_update"
            );

            if (is_callable($functionName)) {
                $updatedCartItemSpec = call_user_func(
                    $functionName,
                    $originalCartItemSpec
                );

                if ($updatedCartItemSpec === null) {
                    return null;
                }

                if (CBConvert::valueAsModel($updatedCartItemSpec) === null) {
                    $message =
                    "The value returned by {$functionName}() should be "
                    . ' either null or a model.';

                    throw CBException::createModelIssueException(
                        $message,
                        $updatedCartItemSpec,
                        '7ded2849d2311f76d4e4ea1404a9581372140816'
                    );
                }

                $updatedCartItemSpecPropertyValue = CBModel::value(
                    $updatedCartItemSpec,
                    'updatedCartItemSpec'
                );

                if ($updatedCartItemSpecPropertyValue !== null) {
                     $title = implode(
                         ' ',
                         [
                             'The updated cart item spec returned by',
                             "{$functionName}()",
                             'has a value for its "updatedCartItemSpec"',
                             'property which indicates that the function is',
                             'most likely responding in a deprecated format.'
                         ]
                     );

                     throw CBException::createModelIssueException(
                         $title,
                         $updatedCartItemSpec,
                         '73d1e9a0f7d792d7abdf900df7e07c75330c9400'
                     );
                }
            } else {
                throw CBException::createModelIssueException(
                    'The class of the $originalCartItemSpec argument to '
                    . __METHOD__
                    . '() has not implemented the SCCartItem_update() '
                    . 'interface.',
                    $originalCartItemSpec,
                    '4006918cd574b94b69bc369f0ca4a4490907080a'
                );
            }

            if ($updatedCartItemSpec !== null) {
                $updatedCartItemSpecClassName = CBModel::valueToString(
                    $updatedCartItemSpec,
                    'className'
                );

                if (empty($updatedCartItemSpecClassName)) {
                    $updatedCartItemSpec->className =
                    $originalCartItemSpecClassName;
                }

                if ($originalCartItemSpecID !== null) {
                    $updatedCartItemSpecID = CBModel::valueAsID(
                        $updatedCartItemSpec,
                        'ID'
                    );

                    if ($updatedCartItemSpecID === null) {
                        $updatedCartItemSpec->ID = $originalCartItemSpecID;
                    }
                }
            }

            return $updatedCartItemSpec;
        } catch (Throwable $throwable) {
            if (SCCartItem::$reportUpdateExceptions) {
                CBErrorHandler::report($throwable);
            }

            return SCCartItem::updateAfterError(
                $originalCartItemSpec,
                $throwable
            );
        }
    }
    /* update() */



    /**
     * @param object $originalCartItemSpec
     * @param Throwable $throwable
     *
     * @return object
     */
    static function updateAfterError(
        stdClass $originalCartItemSpec,
        Throwable $throwable
    ): stdClass {
        $className = CBModel::valueToString(
            $originalCartItemSpec,
            'className'
        );

        $functionName = "{$className}::SCCartItem_updateAfterError";

        if (is_callable($functionName)) {
            return call_user_func(
                $functionName,
                $originalCartItemSpec
            );
        }

        $updatedCartItemSpec = CBModel::clone($originalCartItemSpec);

        $updatedCartItemSpec->message = <<<EOT

            An error occurred when trying to update this item. It will be
            removed from your cart.

        EOT;

        $updatedCartItemSpec->isNotAvailable = true;

        if ($throwable instanceof CBException) {
            $updatedCartItemSpec->sourceID = $throwable->getSourceID();
        }

        $isDeveloper = CBUserGroup::userIsMemberOfUserGroup(
            ColbyUser::getCurrentUserCBID(),
            'CBDevelopersUserGroup'
        );

        if ($isDeveloper) {
            if ($throwable instanceof CBException) {
                $exceptionMessage = $throwable->getExtendedMessage();
            } else {
                $exceptionMessage = CBMessageMarkup::stringToMessage(
                    $throwable->getMessage()
                );
            }

            $updatedCartItemSpec->message = <<<EOT

                {$updatedCartItemSpec->message}

                --- dl
                    --- dt
                        exception message
                    ---
                    --- dd
                        {$exceptionMessage}
                    ---
                ---

            EOT;
        }

        return $updatedCartItemSpec;
    }
    /* updateAfterError() */



    /**
     * @param [mixed] $originalCartItemSpecs
     *
     * @return [object|null]
     */
    static function updateSpecs(array $originalCartItemSpecs): array {
        return array_map(
            function ($cartItemSpec) {
                return SCCartItem::update($cartItemSpec);
            },
            $originalCartItemSpecs
        );
    }

}

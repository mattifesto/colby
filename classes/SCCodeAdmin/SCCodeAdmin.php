<?php

final class SCCodeAdmin {

    /**
     * @return [object]
     */
    static function CBCodeAdmin_searches(): array {
        return [

            /* -- errors -- -- -- -- -- */



            /**
             * 2019_08_27 (warning)
             * 2020_02_22 (error)
             */
            (object)[
                'cbmessage' => <<<EOT

                    The minimum allowed order subtotal should be determined by
                    SCOrderKind.

                EOT,
                'filetype' => 'php',
                'regex' => 'currentOrderSubtotalMinimumInCents',
                'severity' => 3,
                'title' => 'SCOrder::currentOrderSubtotalMinimumInCents()',
            ],


            /**
             * 2020_02_19 (error)
             */
            (object)[
                'args' => '--ignore-case',
                'cbmessage' => <<<EOT

                    Cart items that are invalid or unavailable for other reasons
                    should use SCCartItem.getIsNotAvailable().

                EOT,
                'regex' => 'IsRemovable',
                'severity' => 3,
                'title' => 'SCCartItem.getIsRemovable()',
            ],


            /**
             * 2020_02_19 (error)
             */
            (object)[
                'args' => '--ignore-case',
                'cbmessage' => <<<EOT

                    There is no such thing as a cart item with a constant
                    quantity. A cart item that is unique and singular has an
                    integral quantity that can be adjusted to either 0 or 1.

                    Cart items that are invalid or unavailable for other reasons
                    should use SCCartItem.getIsNotAvailable().

                EOT,
                'regex' => 'HasConstantQuantity',
                'severity' => 3,
                'title' => 'SCCartItem.getHasConstantQuantity()',
            ],


            /**
             * 2020_02_15 (error)
             */
            (object)[
                'cbmessage' => <<<EOT

                    Use CBEmail to send all emails.

                EOT,
                'filetype' => 'php',
                'regex' => 'SHOPPING_CART_EMAIL',
                'severity' => 3,
                'title' => 'Deprecated Shopping Cart Email Constants',
            ],


            /**
             * 2019_11_25 (warning)
             * 2019_12_30 (error)
             */
            (object)[
                'cbmessage' => <<<EOT

                    This function was created during the transition of
                    orders from archives to models. Remove the code that
                    uses it or convert it to
                    CBModels::fetchModelByIDNullable().

                EOT,
                'filetype' => 'php',
                'regex' => 'SCOrder::fetchModelByID',
                'severity' => 3,
                'title' => 'SCOrder::fetchModelByID()',
            ],


            /**
             * 2019_11_25 (warning)
             * 2019_12_30 (error)
             */
            (object)[
                'cbmessage' => <<<EOT

                    This function was created during the transition of
                    orders from archives to models. Remove the code that
                    uses it or convert it to
                    CBModels::fetchSpecByIDNullable().

                EOT,
                'filetype' => 'php',
                'regex' => 'SCOrder::fetchSpecByID',
                'severity' => 3,
                'title' => 'SCOrder::fetchSpecByID()',
            ],


            /**
             * 2019_07_06 (error)
             */
            (object)[
                'filetype' => 'php',
                'regex' => 'STRIPE_LIBRARY_DIRECTORY',
                'severity' => 3,
                'title' => 'STRIPE_LIBRARY_DIRECTORY is no longer used',
            ],


            /**
             * 2019_07_06 (error)
             */
            (object)[
                'filetype' => 'php',
                'regex' => 'STRIPE_PUBLISHABLE_KEY',
                'severity' => 3,
                'title' => 'STRIPE_PUBLISHABLE_KEY is no longer used',
            ],


            /**
             * 2019_07_06 (error)
             */
            (object)[
                'filetype' => 'php',
                'regex' => 'STRIPE_SECRET_KEY',
                'severity' => 3,
                'title' => 'STRIPE_SECRET_KEY is no longer used',
            ],


            /**
             * 2019_08_24 (warning)
             * 2019_08_25 (error)
             */
            (object)[
                'filetype' => 'php',
                'regex' => 'SCShoppingCart::currentShippingRateInCents\(',
                'severity' => 3,
                'title' => 'SCShoppingCart::currentShippingRateInCents()',
                'cbmessage' => <<<EOT

                    SCShoppingCart::currentShippingRateInCents() has been
                    removed.

                EOT
            ],


            /**
             * 2019_08_27 (warning)
             * 2019_08_27 (error)
             */
            (object)[
                'filetype' => 'php',
                'regex' => 'SCOrder::isWholesale\(',
                'severity' => 3,
                'title' => 'SCOrder::isWholesale()',
                'cbmessage' => <<<EOT

                    This function has been replaced by
                    SCOrderKind::liveOrderIsWholesale().

                EOT
            ],



            /**
             * 2019_08_24 (warning)
             * 2019_11_21 (error)
             */
            (object)[
                'filetype' => 'php',
                'regex' => (
                    'currentFreeShippingMinimumInCents'
                ),
                'severity' => 3,
                'title' => (
                    'SCShoppingCart::currentFreeShippingMinimumInCents()'
                ),
                'cbmessage' => <<<EOT

                    SCShoppingCart::currentFreeShippingMinimumInCents() has been
                    deprecated, use whatever method you choose as a replacement
                    in an SCOrderKind interface

                EOT
            ],


            /**
             * 2019_11_25 (error)
             */
            (object)[
                'cbmessage' => <<<EOT

                    Use order models and delete the "keyValueData" column
                    from the SCOrders table.

                    This column is currently completely ready to be removed.
                    It is only written to and never read from for any vital
                    purpose.

                EOT,
                'filetype' => 'php',
                'regex' => 'fetchKeyValueDataByOrderID',
                'severity' => 3,
                'title' => 'SCOrdersTable::fetchKeyValueDataByOrderID()',
            ],


            /**
             * 2019_11_29 (error)
             */
            (object)[
                'cbmessage' => <<<EOT

                    Remove all uses of these classes and then the classes too.

                EOT,
                'filetype' => 'php',
                'regex' => '(CBArchive|ColbyArchive)',
                'severity' => 3,
                'title' => 'CBArchive, ColbyArchive',
            ],



            /* -- warnings -- -- -- -- -- */



            /**
             * 2019_08_25 (warning)
             */
            (object)[
                'filetype' => 'php',
                'regex' => 'SCFlatShippingRateInCents',
                'severity' => 4,
                'title' => 'SCFlatShippingRateInCents',
                'cbmessage' => <<<EOT

                    SCFlatShippingRateInCents is a CBSitePreferences custom
                    variable name used to specify the flat shipping rate. Custom
                    variables are being deprecated and values like this should
                    be specified in code so that they propagate to development,
                    test, and production websites and can be relied on for
                    testing.

                EOT
            ],


            /**
             * 2019_08_25 (warning)
             */
            (object)[
                'filetype' => 'php',
                'regex' => 'SCWholesaleFlatShippingRateInCents',
                'severity' => 4,
                'title' => 'SCWholesaleFlatShippingRateInCents',
                'cbmessage' => <<<EOT

                    SCWholesaleFlatShippingRateInCents is a CBSitePreferences
                    custom variable name used to specify the flat shipping rate.
                    Custom variables are being deprecated and values like this
                    should be specified in code so that they propagate to
                    development, test, and production websites and can be relied
                    on for testing.

                EOT
            ],
        ];
    }
    /* CBCodeAdmin_searches() */
}
/* SCCodeAdmin */

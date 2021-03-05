<?php

final class SCOrderKind {

    /* -- CBAdmin interfaces -- -- -- -- -- */



    /**
     * @return [string]
     */
    static function CBAdmin_getIssueMessages(): array {
        $preferencesModelCBID = SCPreferences::getModelCBID();

        $preferencesModel = CBModelCache::fetchModelByID(
            $preferencesModelCBID
        );

        $defaultOrderKindClassName = CBModel::valueToString(
            $preferencesModel,
            'defaultOrderKindClassName'
        );

        if ($defaultOrderKindClassName !== '') {
            return [];
        }

        $cbmessage = <<<EOT

            This site does not have a default order kind class. Orders cannot
            be processed until it has one. Go to the
            (
                SCPreferences editor
                (
                    a
                    /admin/?c=CBModelEditor&ID={$preferencesModelCBID}
                )
            )
            to generate one.

        EOT;

        return [
            $cbmessage,
        ];
    }
    /* CBAdmin_getIssueMessages() */



    /* -- CBAjax interfaces -- -- -- -- -- */



    /**
     * @param object $args
     *
     * @return void
     */
    static function CBAjax_generateDefaultOrderKindClass(
        stdClass $args
    ): void {
        $orderKindClassName = CBModel::valueAsName(
            $args,
            'orderKindClassName'
        );

        if ($orderKindClassName === null) {
            throw new CBExceptionWithValue(
                'The "orderKindClassName" property is not a valid name.',
                $args,
                '4e033fb78ffb6852a689cdfbf52568be30226022'
            );
        }

        $destinationDirectory = (
            cbsitedir() .
            "/classes/{$orderKindClassName}"
        );

        if (!is_dir($destinationDirectory)) {
            mkdir($destinationDirectory);
        }

        $content = file_get_contents(
            __DIR__ . '/SCOrderKind_template.data'
        );

        $content = preg_replace(
            '/CLASSNAME/',
            $orderKindClassName,
            $content
        );

        $destinationFilepath = (
            $destinationDirectory .
            "/{$orderKindClassName}.php"
        );

        file_put_contents(
            $destinationFilepath,
            $content
        );

        CBInstall::install();
    }
    /* CBAjax_generateDefaultOrderKindClass() */



    /**
     * @return string
     */
    static function CBAjax_generateDefaultOrderKindClass_getUserGroupClassName(
    ): string {
        return 'CBDevelopersUserGroup';
    }



    /* -- functions -- -- -- -- -- */



    /**
     * @param string $orderKindClassName
     * @param string $countryCode
     *
     *      Examples:
     *
     *          "US"
     *          "3c28bcd6efa51ea277df8ca6a77d813abc7c1c39"
     *
     * @return string
     *
     *      Examples:
     *
     *          "United States"
     *          "Zimbabwe"
     */
    static function countryCodeToCountryName(
        string $orderKindClassName,
        string $countryCode
    ): string {
        $functionName = (
            $orderKindClassName .
            '::SCOrderKind_countryCodeToCountryName'
        );

        if (is_callable($functionName)) {
            return call_user_func(
                $functionName,
                $countryCode
            );
        } else {
            throw new CBException(
                CBConvert::stringToCleanLine(<<<EOT

                    The order kind class "${orderKindClassName}" has not
                    implemented the SCOrderKind_countryCodeToCountryName()
                    interface.

                EOT),
                '',
                '362b3ac3332031b6ec058ab842177f73518ab001'
            );
        }
    }
    /* countryCodeToCountryName() */



    /**
     * @param string $orderKindClassName
     *
     * @return [object]
     *
     *      Example:
     *
     *      [
     *          {
     *              title: "United States",
     *              isDefault: true,
     *              value: "25fdd2e5dff953fe251afa2c49a42a80f8c70777"
     *          },
     *          {
     *              title: "Zimbabwe",
     *              value: "f488bdcd244af0f69a5ca1aef7a7d19d32810d96"
     *          },
     *      ]
     */
    static function countryOptions(
        string $orderKindClassName
    ): array {
        $functionName = (
            $orderKindClassName .
            '::SCOrderKind_countryOptions'
        );

        if (is_callable($functionName)) {
            return call_user_func(
                $functionName
            );
        } else {
            throw new CBException(
                CBConvert::stringToCleanLine(<<<EOT

                    The order kind class "${orderKindClassName}" has not
                    implemented the SCOrderKind_countryOptions() interface.

                EOT),
                '',
                '4ce6d6f6b392c731c91c3b412d8b1b03432cd133'
            );
        }
    }
    /* countryOptions() */



    /**
     * This function converts the country option value passed from the client in
     * the shipping address to the value that will be used as the country code
     * in the SCOrder spec and model.
     *
     * The term "country code" once referred to a two letter code for a country,
     * but now may also refer to an ID of a country model. Classes that
     * implement SCOrderKind interfaces can decide what is most appropriate.
     *
     * @param string $orderKindClassName
     * @param string $countyOptionValue
     *
     *      Example:
     *
     *          "8f8141b025441662fb7eda1f234362b5b550f265"
     *          "US"
     *
     * @return string
     *
     *      Examples:
     *
     *          "662ca761c067680d44a742ef34b4ee6d496c35ad"
     *          "US"
     */
    static function countryOptionValueToCountryCode(
        string $orderKindClassName,
        string $countyOptionValue
    ): string {
        $functionName = (
            $orderKindClassName .
            '::SCOrderKind_countryOptionValueToCountryCode'
        );

        if (is_callable($functionName)) {
            return call_user_func(
                $functionName,
                $countyOptionValue
            );
        } else {
            throw new CBException(
                CBConvert::stringToCleanLine(<<<EOT

                    The order kind class "${orderKindClassName}" has not
                    implemented the
                    SCOrderKind_countryOptionValueToCountryCode() interface.

                EOT),
                '',
                'a777579b48d915a891ca06858f0df16cd60ce9c9'
            );
        }
    }
    /* countryOptionValueToCountryCode() */



    /**
     * This function gets the title for the default order shipping method, such
     * as "Flat Rate" or "The Very Best International Second Day Air".
     *
     * In the future customers will choose their shipping method. When that
     * feature is added, each shipping method will probably have model with a
     * CBID and other properties such as a title. One of those those shipping
     * methods will be selected and this function will change or be deprecated
     * and removed.
     *
     * @param string $orderKindClassName
     *
     * @return string
     */
    static function defaultShippingMethod(
        string $orderKindClassName
    ): string {
        $functionName = (
            $orderKindClassName .
            '::SCOrderKind_defaultShippingMethod'
        );

        if (is_callable($functionName)) {
            return call_user_func(
                $functionName
            );
        } else {
            throw new CBException(
                CBConvert::stringToCleanLine(<<<EOT

                    The order kind class "${orderKindClassName}" has not
                    implemented the SCOrderKind_defaultShippingMethod()
                    interface.

                EOT),
                '',
                'd01f1b0f23e594184289541188279d235c163af9'
            );
        }
    }
    /* defaultShippingMethod() */



    /**
     * For some orders, such as wholesale orders, the customer is required to
     * make a minimum purchase.
     *
     * @param object $orderSpec
     *
     *      This function is used during the prepare phase. It will work with
     *      an order model but the returned value is not very useful once an
     *      order has been prepared and built.
     *
     * @return int
     *
     *      Returns 0 if the order kind class has not implemented
     *      SCOrderKind_getMinimumSubtotalInCents() because that is such a
     *      common value there is no need to force implementation.
     */
    static function getMinimumSubtotalInCents(
        stdClass $orderSpec
    ): int {
        $orderKindClassName = CBModel::valueAsName(
            $orderSpec,
            'kindClassName'
        );

        /**
         * If this function is being called, the "kindClassName" property on the
         * order spec should already have been set.
         */
        if ($orderKindClassName === null) {
            throw new CBExceptionWithValue(
                'The "kindClassName" property value is not valid.',
                $orderSpec,
                'da2002dd28c58e01d071ca335c544b3870a4118a'
            );
        }

        $functionName = (
            $orderKindClassName .
            '::SCOrderKind_getMinimumSubtotalInCents'
        );

        if (is_callable($functionName)) {
            return call_user_func(
                $functionName,
                $orderSpec
            );
        }

        return 0;
    }
    /* getMinimumSubtotalInCents() */



    /**
     * This function is called when a live order (an order being made on the
     * website) is created to give the SCOrderKind class a chance to determine
     * whether the order is a wholesale order, usually by looking at properties
     * of the current logged in user.
     *
     * For non-live orders, the spec's "isWholesale" property is set by the
     * creator to whatever value they wish it to have.
     *
     * @return bool
     */
    static function liveOrderIsWholesale(
        stdClass $originalOrderSpec
    ): bool {
        $orderKindClassName = CBModel::valueToString(
            $originalOrderSpec,
            'kindClassName'
        );

        $functionName = (
            $orderKindClassName .
            '::SCOrderKind_liveOrderIsWholesale'
        );

        if (is_callable($functionName)) {
            return call_user_func(
                $functionName,
                $originalOrderSpec
            );
        } else {
            return false;
        }
    }
    /* liveOrderIsWholesale() */



    /**
     * @see documentation
     *
     * @return [string]
     */
    static function orderToCBMessages(
        stdClass $orderModel
    ): array {
        $orderKindClassName = CBModel::valueToString(
            $orderModel,
            'kindClassName'
        );

        $functionName = (
            $orderKindClassName .
            '::SCOrderKind_orderToCBMessages'
        );

        if (is_callable($functionName)) {
            return call_user_func(
                $functionName,
                $orderModel
            );
        } else {
            return [];
        }
    }
    /* orderToMessages() */



    /**
     * @TODO 2021_02_22
     *
     *      Replace this function with a new function named
     *      calculateSalesTaxInCents() that gets the order kind class name
     *      from the spec.
     *
     *      Actually, I feel like this function should be simpler too. It should
     *      get the taxable amount instead of the whole order. This function is
     *      called while we are preparing the order so the order will be in an
     *      odd state at the time this function is called. It would be best not
     *      to expect this function to be able to call functions with the order
     *      during that time. (It needs the address too... hmmm...)
     *
     * @param string $orderKindClassName
     * @param object $orderSpec
     *
     * @return int
     *
     *      This function returns the amount of sales tax that should be charged
     *      for the order spec given.
     */
    static function
    salesTaxInCents(
        string $orderKindClassName,
        stdClass $orderSpec
    ): int {
        $functionName = (
            $orderKindClassName .
            '::SCOrderKind_salesTaxInCents'
        );

        if (is_callable($functionName)) {
            return call_user_func(
                $functionName,
                $orderSpec
            );
        } else {
            throw new CBException(
                CBConvert::stringToCleanLine(<<<EOT

                    The order kind class "${orderKindClassName}" has not
                    implemented the SCOrderKind_salesTaxInCents() interface.

                EOT),
                '',
                'f8d9ae7fda4f9c24ab641e2bf24372733bb706cc'
            );
        }
    }
    /* salesTaxInCents() */



    /**
     * @TODO 2019_08_23
     *
     *      Replace this function with a new function named
     *      calculateShippingChargeInCents() that gets the order kind class name
     *      from the spec.
     *
     * @param string $orderKindClassName
     * @param object $orderSpec
     *
     * @return int
     */
    static function shippingChargeInCents(
        string $orderKindClassName,
        stdClass $orderSpec
    ): int {
        $functionName = (
            $orderKindClassName .
            '::SCOrderKind_shippingChargeInCents'
        );

        if (is_callable($functionName)) {
            return call_user_func(
                $functionName,
                $orderSpec
            );
        } else {
            throw new CBException(
                CBConvert::stringToCleanLine(<<<EOT

                    The order kind class "${orderKindClassName}" has not
                    implemented the SCOrderKind_shippingChargeInCents()
                    interface.

                EOT),
                '',
                '7365979f971f4ce81f8db388e892df387060e42f'
            );
        }
    }
    /* shippingChargeInCents() */

}

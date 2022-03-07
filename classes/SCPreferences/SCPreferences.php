<?php

final class
SCPreferences
{
    /**
     * This variable will be set to a substitute ID to be used by SCPreferences
     * while tests are running.
     */
    static $testID = null;



    /* -- CBHTMLOutput interfaces -- -- -- -- -- */



    /**
     * @return [string]
     */
    static function
    CBHTMLOutput_JavaScriptURLs(
    ): array {
        return [
            Colby::flexpath(
                __CLASS__,
                'v675.1.js',
                cbsysurl()
            ),
        ];
    }
    /* CBHTMLOutput_JavaScriptURLs() */



    /**
     * @return [string]
     */
    static function
    CBHTMLOutput_requiredClassNames(
    ): array {
        return [
            'CBModel',
        ];
    }
    /* CBHTMLOutput_requiredClassNames() */



    /* -- CBInstall interfaces -- -- -- -- -- */



    /**
     * @return void
     */
    static function CBInstall_install(): void {
        CBModelUpdater::update(
            (object)[
                'className' => 'SCPreferences',
                'ID' => SCPreferences::getModelCBID(),
                'cartItemClassNames' => [],
                'defaultOrderKindClassName' => '',
            ]
        );
    }
    /* CBInstall_install() */



    /**
     * @return [string]
     */
    static function CBInstall_requiredClassNames(): array {
        return [
            'CBModelUpdater',
        ];
    }



    /* -- CBModel interfaces -- -- -- -- -- */



    /**
     * @param object $spec
     *
     *      {
     *          orderNotificationsEmailAddressesCSV: string
     *      }
     *
     * @return object
     *
     *      {
     *          orderNotificationsEmailAddresses: [string]
     *      }
     */
    static function
    CBModel_build(
        stdClass $spec
    ): stdClass
    {
        $orderNotificationsEmailAddresses = (
            SCPreferences::stringToArrayOfEmailAddresses(
                SCPreferences::getOrderNotificationsEmailAddressesCSV(
                    $spec
                )
            )
        );

        $preferencesModel =
        (object)[
            'cartItemCartViewClassNames' => CBModel::valueToArray(
                $spec,
                'cartItemCartViewClassNames'
            ),

            'cartItemCheckoutViewClassNames' => CBModel::valueToArray(
                $spec,
                'cartItemCheckoutViewClassNames'
            ),

            'cartItemClassNames' => CBModel::valueToArray(
                $spec,
                'cartItemClassNames'
            ),

            'cartItemOrderViewClassNames' => CBModel::valueToArray(
                $spec,
                'cartItemOrderViewClassNames'
            ),

            'defaultOrderKindClassName' => (
                SCPreferences::getDefaultOrderKindClassName(
                    $spec
                )
            ),

            'sendingEmailAddress' => trim(
                CBModel::valueToString($spec, 'sendingEmailAddress')
            ),

            'sendingEmailName' => trim(
                CBModel::valueToString($spec, 'sendingEmailName')
            ),

            'SMTPServerHostname' => trim(
                CBModel::valueToString($spec, 'SMTPServerHostname')
            ),

            'SMTPServerPort' => trim(
                CBModel::valueToString($spec, 'SMTPServerPort')
            ),

            'SMTPServerSecurity' => trim(
                CBModel::valueToString($spec, 'SMTPServerSecurity')
            ),

            'SMTPServerUsername' => trim(
                CBModel::valueToString($spec, 'SMTPServerUsername')
            ),

            'SMTPServerPassword' => trim(
                CBModel::valueToString($spec, 'SMTPServerPassword')
            ),

            'orderNotificationsEmailAddresses' => (
                $orderNotificationsEmailAddresses
            ),
        ];


        SCPreferences::setSpecialInstructionCBMessage(
            $preferencesModel,
            SCPreferences::getSpecialInstructionCBMessage(
                $spec
            )
        );


        return $preferencesModel;
    }
    /* CBModel_build() */



    /* -- accessors -- */



    /**
     * @param object $model
     *
     * @return string
     */
    static function
    getDefaultOrderKindClassName(
        stdClass $model
    ): string {
        return CBModel::valueToString(
            $model,
            'defaultOrderKindClassName'
        );
    }
    /* getDefaultOrderKindClassName() */



    /**
     * @param object $spec
     * @param string $defaultOrderKindClassName
     *
     * @return void
     */
    static function
    setDefaultOrderKindClassName(
        stdClass $spec,
        string $defaultOrderKindClassName
    ): void {
        if ($defaultOrderKindClassName !== '') {
            if (!CBConvert::valueIsName($defaultOrderKindClassName)) {
                throw new CBExceptionWithValue(
                    CBConvert::stringToCleanLine(<<<EOT

                        The "defaultOrderKindClassName" argument is not a valid
                        name.

                    EOT),
                    $defaultOrderKindClassName,
                    'df45242393f540a527549376f1df1a12b378b99b'
                );
            }
        }

        $spec->defaultOrderKindClassName = $defaultOrderKindClassName;
    }
    /* getDefaultOrderKindClassName() */



    /**
     * @param object $preferencesModel
     *
     * @return string
     */
    static function
    getSpecialInstructionCBMessage(
        stdClass $preferencesModel
    ): string
    {
        return CBModel::valueToString(
            $preferencesModel,
            'SCPreferences_specialInstructionsCBMessage_property'
        );
    }
    // getSpecialInstructionCBMessage()



    /**
     * @param object $preferencesModel
     * @param string $newSpecialInstructionsCBMessage
     *
     * @return void
     */
    static function
    setSpecialInstructionCBMessage(
        stdClass $preferencesModel,
        string $newSpecialInstructionsCBMessage
    ): void
    {
        $preferencesModel->SCPreferences_specialInstructionsCBMessage_property =
        $newSpecialInstructionsCBMessage;
    }
    // setSpecialInstructionCBMessage()



    /* -- functions -- */



    /**
     * @return CBID
     */
    static function getModelCBID(): string {
        return (
            SCPreferences::$testID ??
            'be64c47012a0a49e1bed962979c67918c02caad6'
        );
    }



    /**
     * @param object $spec
     *
     *      This function should only be used on models because the value
     *      returned does not exist on specs.
     *
     * @return [string]
     *
     *      Returns an array of email addresses that should receive
     *      notifications about new orders.
     */
    static function
    getOrderNotificationsEmailAddresses(
        stdClass $model
    ): array {
        return CBModel::valueToArray(
            $model,
            'orderNotificationsEmailAddresses'
        );
    }
    /* getOrderNotificationsEmailAddresses() */



    /**
     * @param object $spec
     *
     *      This function should only be used on specs because build converts
     *      this property into a different property on a model.
     *
     * @return string
     *
     *      Returns a string that is a CSV of email addresses.
     */
    static function
    getOrderNotificationsEmailAddressesCSV(
        stdClass $spec
    ): string {
        return CBModel::valueToString(
            $spec,
            'orderNotificationsEmailAddressesCSV'
        );
    }
    /* getOrderNotificationsEmailAddressesCSV() */



    /**
     * @deprecated use SCPreferences::getModelCBID()
     *
     * @return ID
     */
    static function ID(): string {
        return SCPreferences::getModelCBID();
    }



    /**
     * @param string $cartItemCartViewClassName
     *
     * @return void
     */
    static function installCartItemCartViewClass(
        string $cartItemCartViewClassName
    ): void {
        if ($cartItemCartViewClassName === '') {
            throw new InvalidArgumentException(
                'The $cartItemCartViewClassName argument to ' .
                __METHOD__ .
                '() cannot be an empty string.'
            );
        }

        $updater = CBModelUpdater::fetch(
            (object)[
                'ID' => SCPreferences::getModelCBID(),
            ]
        );

        $spec = $updater->working;

        $cartItemCartViewClassNames = CBModel::valueToArray(
            $spec,
            'cartItemCartViewClassNames'
        );

        array_push(
            $cartItemCartViewClassNames,
            $cartItemCartViewClassName
        );

        $spec->cartItemCartViewClassNames = $cartItemCartViewClassNames;

        CBModelUpdater::save($updater);
    }
    /* installCartItemCartViewClass() */



    /**
     * @param string $cartItemCheckoutViewClassName
     *
     * @return void
     */
    static function installCartItemCheckoutViewClass(
        string $cartItemCheckoutViewClassName
    ): void {
        if ($cartItemCheckoutViewClassName === '') {
            throw new InvalidArgumentException(
                'The $cartItemCheckoutViewClassName argument to ' .
                __METHOD__ .
                '() cannot be an empty string.'
            );
        }

        $updater = CBModelUpdater::fetch(
            (object)[
                'ID' => SCPreferences::getModelCBID(),
            ]
        );

        $spec = $updater->working;

        $cartItemCheckoutViewClassNames = CBModel::valueToArray(
            $spec,
            'cartItemCheckoutViewClassNames'
        );

        array_push(
            $cartItemCheckoutViewClassNames,
            $cartItemCheckoutViewClassName
        );

        $spec->cartItemCheckoutViewClassNames = $cartItemCheckoutViewClassNames;

        CBModelUpdater::save($updater);
    }
    /* installCartItemCheckoutViewClass() */



    /**
     * @param string $cartItemClassName
     *
     * @return void
     */
    static function installCartItemClass(string $cartItemClassName): void {
        if ($cartItemClassName === '') {
            throw new InvalidArgumentException(
                'The $cartItemClassName argument to ' .
                __METHOD__ .
                '() cannot be an empty string.'
            );
        }

        $updater = CBModelUpdater::fetch(
            (object)[
                'ID' => SCPreferences::getModelCBID(),
            ]
        );

        $spec = $updater->working;

        $cartItemClassNames = CBModel::valueToArray(
            $spec,
            'cartItemClassNames'
        );

        array_push(
            $cartItemClassNames,
            $cartItemClassName
        );

        $spec->cartItemClassNames = $cartItemClassNames;

        CBModelUpdater::save($updater);
    }
    /* installCartItemClass() */



    /**
     * @param string $cartItemOrderViewClassName
     *
     * @return void
     */
    static function installCartItemOrderViewClass(
        string $cartItemOrderViewClassName
    ): void {
        if ($cartItemOrderViewClassName === '') {
            throw new InvalidArgumentException(
                'The $cartItemOrderViewClassName argument to ' .
                __METHOD__ .
                '() cannot be an empty string.'
            );
        }

        $updater = CBModelUpdater::fetch(
            (object)[
                'ID' => SCPreferences::getModelCBID(),
            ]
        );

        $spec = $updater->working;

        $cartItemOrderViewClassNames = CBModel::valueToArray(
            $spec,
            'cartItemOrderViewClassNames'
        );

        array_push(
            $cartItemOrderViewClassNames,
            $cartItemOrderViewClassName
        );

        $spec->cartItemOrderViewClassNames = $cartItemOrderViewClassNames;

        CBModelUpdater::save($updater);
    }
    /* installCartItemOrderViewClass() */



    /**
     * @param string $csvOfEmailAddresses
     *
     * @return [string]
     */
    private static function
    stringToArrayOfEmailAddresses(
        string $csvOfEmailAddresses
    ): array {
        /**
         * The function str_getcsv() will return [null] for an empty string so
         * we detect the empty string before calling that function.
         */
        if ($csvOfEmailAddresses === '') {
            return [];
        }

        $arrayOfEmailAddresses = str_getcsv(
            $csvOfEmailAddresses
        );

        $arrayOfEmailAddresses = array_map(
            function (
                string $potentialEmailAddress
            ) {
                return CBConvert::valueAsEmail(
                    $potentialEmailAddress
                );
            },
            $arrayOfEmailAddresses
        );

        $arrayOfEmailAddresses = array_filter(
            $arrayOfEmailAddresses
        );

        return array_values($arrayOfEmailAddresses);
    }
    /* stringToArrayOfEmailAddresses() */

}

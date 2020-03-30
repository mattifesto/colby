<?php

final class SCPreferences {

    /**
     * This variable will be set to a substitute ID to be used by SCPreferences
     * while tests are running.
     */
    static $testID = null;



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
     * @return stdClass
     */
    static function CBModel_build(
        stdClass $spec
    ): stdClass {
        return (object)[
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
            'defaultOrderKindClassName' => trim(
                CBModel::valueToString($spec, 'defaultOrderKindClassName')
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
        ];
    }
    /* CBModel_build() */



    /* -- functions -- -- -- -- -- */



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

}

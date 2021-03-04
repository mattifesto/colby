<?php

final class SCOrderInspector {

    /* -- CBAdmin interfaces -- -- -- -- -- */



    /**
     * @return string
     */
    static function CBAdmin_getUserGroupClassName(): string {
        return 'CBAdministratorsUserGroup';
    }



    /**
     * @return [string]
     */
    static function CBAdmin_menuNamePath() {
        return [
            'orders'
        ];
    }



    /**
     * @return void
     */
    static function CBAdmin_render(): void {
        $orderID = cb_query_string_value('ID');

        if (empty($orderID)) {
            return;
        }

        $model = CBModels::fetchModelByIDNullable($orderID);
        $info = CBHTMLOutput::pageInformation();

        $info->title = (
            'Order ' .
            CBModel::valueAsInt($model, 'orderRowId') ?? 'error'
        );

        $info->publishedTimestamp = CBModel::valueAsInt($model, 'orderCreated');
    }
    /* CBAdmin_render() */



    /* -- CBAjax interfaces -- -- -- -- -- */



    /**
     * @param object $args
     *
     *      {
     *          isArchived: bool
     *          orderID: ID
     *      }
     *
     * @return void
     */
    static function CBAjax_setIsArchived(stdClass $args): void {
        $requestedIsArchived = CBModel::valueToBool($args, 'isArchived');
        $orderID = CBModel::valueAsID($args, 'orderID');

        if (empty($orderID)) {
            throw new InvalidArgumentException(
                'A valid "orderID" argument is required.'
            );
        }

        $originalSpec = CBModels::fetchSpecByIDNullable($orderID);

        $className = CBModel::valueToString(
            $originalSpec,
            'className'
        );

        if (empty($originalSpec) || $className !== "SCOrder") {
            throw new Exception(
                'The "orderID" argument does not reference an order.'
            );
        }

        $spec = CBModel::clone($originalSpec);

        $originalIsArchived = CBModel::valueAsInt(
            $spec,
            'orderArchived'
        ) !== null;

        if ($requestedIsArchived && !$originalIsArchived) {
            $spec->orderArchived = time();
            $spec->orderArchivedByUserCBID = ColbyUser::getCurrentUserCBID();
        } else if (!$requestedIsArchived && $originalIsArchived) {
            unset($spec->orderArchived);
            unset($spec->orderArchivedByUserCBID);
        }

        if ($spec != $originalSpec) {
            CBDB::transaction(
                function () use ($requestedIsArchived, $spec) {
                    $isArchivedForSQL = $requestedIsArchived ? "b'1'" : "b'0'";
                    $orderIDAsSQL = CBID::toSQL($spec->ID);

                    CBModels::save($spec);

                    $SQL = <<<EOT

                        UPDATE  SCOrders
                        SET     isArchived = {$isArchivedForSQL}
                        WHERE   archiveId = {$orderIDAsSQL}

                    EOT;

                    Colby::query($SQL);
                }
            );
        }
    }
    /* CBAjax_setIsArchived() */



    /**
     * @return string
     */
    static function CBAjax_setIsArchived_getUserGroupClassName(): string {
        return 'CBAdministratorsUserGroup';
    }



    /* -- CBHTMLOutput interfaces -- -- -- -- -- */



    /**
     * @return [string]
     */
    static function CBHTMLOutput_CSSURLs(): array {
        return [
            Colby::flexpath(__CLASS__, 'v101.css', scliburl()),
        ];
    }



    /**
     * @return [string]
     */
    static function
    CBHTMLOutput_JavaScriptURLs(
    ): array {
        return [
            Colby::flexpath(
                __CLASS__,
                'v675.14.js',
                cbsysurl()
            ),
        ];
    }
    /* CBHTMLOutput_JavaScriptURLs() */



    /**
     * @return [[<name>, <value>]]
     */
    static function CBHTMLOutput_JavaScriptVariables(): array {
        $orderID = cb_query_string_value('ID');
        $model = CBModels::fetchModelByIDNullable($orderID);

        $isDeveloper = CBUserGroup::userIsMemberOfUserGroup(
            ColbyUser::getCurrentUserCBID(),
            'CBDevelopersUserGroup'
        );

        if ($isDeveloper) {

            if (empty($orderID)) {
                return [];
            }

            $variables = [
                [
                    'SCOrderInspector_userIsADeveloper',
                    true,
                ],
                [
                    'SCOrderInspector_emailHTML',
                    SCOrderConfirmationEmail::messageHTML($model),
                ],
                [
                    'SCOrderInspector_emailText',
                    SCOrderConfirmationEmail::messageText($model),
                ],
                [
                    'SCOrderInspector_orderSummaryCBMessage',
                    SCOrder::toSummaryCBMesssage(
                        $model
                    ),
                ],
            ];
        } else {
            $variables = [
                [
                    'SCOrderInspector_userIsADeveloper',
                    false,
                ],
            ];
        }

        $isArchived = CBModel::valueAsInt($model, 'orderArchived') !== null;

        return array_values(
            array_merge(
                $variables,
                [
                    [
                        'SCOrderInspector_model',
                        $model,
                    ],
                    [
                        'SCOrderInspector_orderID',
                        $orderID,
                    ],
                    [
                        'SCOrderInspector_originalIsArchived',
                        $isArchived,
                    ],
                    [
                        'SCOrderInspector_wholesaleCustomerModel',
                        SCOrderInspector::fetchWholesaleCustomerModel(
                            $model
                        ),
                    ],
                ]
            )
        );
    }
    /* CBHTMLOutput_JavaScriptVariables() */



    /**
     * @return [string]
     */
    static function CBHTMLOutput_requiredClassNames(): array {
        $preferencesModel = CBModelCache::fetchModelByID(
            SCPreferences::ID()
        );

        $cartItemClassNames = CBModel::valueToArray(
            $preferencesModel,
            'cartItemClassNames'
        );

        $cartItemOrderViewClassNames = CBModel::valueToArray(
            $preferencesModel,
            'cartItemOrderViewClassNames'
        );

        return array_merge(
            [
                'CBAjax',
                'CBContentStyleSheet',
                'CBConvert',
                'CBMessageMarkup',
                'CBModel',
                'CBNoteView',
                'CBUI',
                'CBUIBooleanSwitchPart',
                'CBUIExpander',
                'CBUINavigationView',
                'CBUIPanel',
                'CBUISectionItem4',
                'CBUIStringEditor',
                'CBUIStringsPart',
                'CBUser',
                'CBView',
                'Colby',
                'SCCartItem',
            ],
            $cartItemClassNames,
            $cartItemOrderViewClassNames
        );
    }
    /* CBHTMLOutput_requiredClassNames() */



    /**
     * @param object $orderModel
     *
     * @return object|null
     */
    private static function fetchWholesaleCustomerModel(
        stdClass $orderModel
    ): ?stdClass {
        $orderIsWholesale = CBModel::valueToBool($orderModel, 'isWholesale');
        $customerUserID = CBModel::valueAsID($orderModel, 'customerHash');

        if ($orderIsWholesale === false || $customerUserID === null) {
            return null;
        }

        $wholesaleCustomerModelID =
        CBUserToLEWholesaleCustomerAssociation::fetchID(
            $customerUserID
        );

        if (empty($wholesaleCustomerModelID)) {
            return null;
        }

        $wholesaleCustomerModel = CBModels::fetchModelByIDNullable(
            $wholesaleCustomerModelID
        );

        return $wholesaleCustomerModel;
    }
    /* fetchWholesaleUserInformation() */

}

<?php

final class
CBNotification {

    /* -- CBModel interfaces -- */



    /**
     * @param object $notificationSpec
     *
     * @return object
     */
    static function
    CBModel_build(
        stdClass $notificationSpec
    ): stdClass {
        $notificationModel = (object)[];

        $targetUserCBID = CBNotification::getTargetUserCBID(
            $notificationSpec
        );

        $targetUserGroupCBID = CBNotification::getTargetUserGroupCBID(
            $notificationSpec
        );

        if (
            $targetUserCBID === null &&
            $targetUserGroupCBID === null
        ) {
            throw new CBExceptionWithValue(
                CBConvert::stringToCleanLine(<<<EOT

                    This CBNotification spec does not specify a target user or a
                    target user group. A CBNotification spec must specify one or
                    both of those.

                EOT),
                $notificationSpec,
                '591fd553f73fd4f9e11284a9962432826f3fb51f'
            );
        }

        CBNotification::setTargetUserCBID(
            $notificationModel,
            $targetUserCBID
        );

        CBNotification::setTargetUserGroupCBID(
            $notificationModel,
            $targetUserGroupCBID
        );

        $targetModelCBID = CBNotification::getTargetModelCBID(
            $notificationSpec
        );

        if ($targetModelCBID === null) {
            throw new CBExceptionWithValue(
                CBConvert::stringToCleanLine(<<<EOT

                    This CBNotification spec does not specify a target model. A
                    CBNotification spec must specify a target model.

                EOT),
                $notificationSpec,
                '034c11fd9d092ec758feffc23cce2acfe64cb3f7'
            );
        }

        CBNotification::setTargetModelCBID(
            $notificationModel,
            $targetModelCBID
        );

        CBNotification::setCBMessage(
            $notificationModel,
            CBNotification::getCBMessage(
                $notificationSpec
            )
        );

        return $notificationModel;
    }
    /* CBModel_build() */



    /* -- accessors -- */



    /**
     * @param object $notificationModel
     *
     * @return string
     */
    static function
    getCBMessage(
        stdClass $notificationModel
    ): ?string {
        return CBModel::valueAsCBID(
            $notificationModel,
            'CBNotification_cbmessage'
        );
    }
    /* getCBMessage() */



    /**
     * @param object $notificationModel
     * @param string $cbmessage
     *
     * @return void
     */
    static function
    setCBMessage(
        stdClass $notificationModel,
        string $cbmessage
    ): void {
        $notificationModel->CBNotification_cbmessage = $cbmessage;
    }
    /* setCBMessage() */



    /**
     * @param object $notificationModel
     *
     * @return CBID|null
     */
    static function
    getTargetModelCBID(
        stdClass $notificationModel
    ): ?string {
        return CBModel::valueAsCBID(
            $notificationModel,
            'CBNotification_targetModelCBID'
        );
    }
    /* getTargetModelCBID() */



    /**
     * @param object $notificationModel
     * @param CBID|null $targetModelCBID
     *
     * @return void
     */
    static function
    setTargetModelCBID(
        stdClass $notificationModel,
        ?string $targetModelCBID
    ): void {
        if (
            $targetModelCBID !== null &&
            CBID::valueIsCBID($targetModelCBID) !== true
        ) {
            throw new CBExceptionWithValue(
                CBConvert::stringToCleanLine(<<<EOT

                    The targetModelCBID argument passed to this function is not
                    valid.

                EOT),
                $targetModelCBID,
                'a8e9f5c9d62a8dbbef8db0fa1b95ee75a072eb07'
            );
        }

        $notificationModel->CBNotification_targetModelCBID = (
            $targetModelCBID
        );
    }
    /* setTargetModelCBID() */



    /**
     * @param object $notificationModel
     *
     * @return CBID|null
     */
    static function
    getTargetUserCBID(
        stdClass $notificationModel
    ): ?string {
        return CBModel::valueAsCBID(
            $notificationModel,
            'CBNotification_targetUserCBID'
        );
    }
    /* getTargetUserCBID() */



    /**
     * @param object $notificationModel
     * @param CBID|null $targetUserCBID
     *
     * @return void
     */
    static function
    setTargetUserCBID(
        stdClass $notificationModel,
        ?string $targetUserCBID
    ): void {
        if (
            $targetUserCBID !== null &&
            CBID::valueIsCBID($targetUserCBID) !== true
        ) {
            throw new CBExceptionWithValue(
                CBConvert::stringToCleanLine(<<<EOT

                    The targetUserCBID argument passed to this function is not
                    valid.

                EOT),
                $targetUserCBID,
                'a8e9f5c9d62a8dbbef8db0fa1b95ee75a072eb07'
            );
        }

        $notificationModel->CBNotification_targetUserCBID = (
            $targetUserCBID
        );
    }
    /* setTargetUserCBID() */



    /**
     * @param object $notificationModel
     *
     * @return CBID|null
     */
    static function
    getTargetUserGroupCBID(
        stdClass $notificationModel
    ): ?string {
        return CBModel::valueAsCBID(
            $notificationModel,
            'CBNotification_targetUserGroupCBID'
        );
    }
    /* getTargetUserGroupCBID() */



    /**
     * @param object $notificationModel
     * @param CBID|null $targetUserGroupCBID
     *
     * @return void
     */
    static function
    setTargetUserGroupCBID(
        stdClass $notificationModel,
        ?string $targetUserGroupCBID
    ): void {
        if (
            $targetUserGroupCBID !== null &&
            CBID::valueIsCBID($targetUserGroupCBID) !== true
        ) {
            throw new CBExceptionWithValue(
                CBConvert::stringToCleanLine(<<<EOT

                    The targetUserGroupCBID argument passed to this function is
                    not valid.

                EOT),
                $targetUserGroupCBID,
                'a8e9f5c9d62a8dbbef8db0fa1b95ee75a072eb07'
            );
        }

        $notificationModel->CBNotification_targetUserGroupCBID = (
            $targetUserGroupCBID
        );
    }
    /* setTargetUserGroupCBID() */

}

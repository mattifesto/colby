<?php

final class CBDevelopersUserGroup {

    /* -- CBInstall interfaces -- -- -- -- -- */



    /**
     * @return void
     */
    static function CBInstall_install(): void {
        $groupCBID = CBUserGroup::userGroupClassNameToCBID(
            __CLASS__
        );

        CBModelUpdater::update(
            (object)[
                'ID' => $groupCBID,
                'className' => 'CBUserGroup',
                'deprecatedGroupName' => 'Developers',
                'userGroupClassName' => __CLASS__,
            ]
        );

        $tableName = 'ColbyUsersWhoAreDevelopers';

        $SQL = <<<EOT

            SELECT  COUNT(*)

            FROM    information_schema.TABLES

            WHERE   TABLE_SCHEMA = DATABASE() AND
                    TABLE_NAME = '{$tableName}'

        EOT;

        $count = CBConvert::valueAsInt(
            CBDB::SQLToValue($SQL)
        );

        if ($count === 0) {
            return;
        }

        $SQL = <<<EOT

            SELECT  userID

            FROM    {$tableName}

        EOT;

        $userNumericIDs = CBDB::SQLToArrayOfNullableStrings($SQL);

        $userCBIDs = CBUsers::userNumericIDsToUserCBIDs($userNumericIDs);

        CBUserGroup::addUsers(__CLASS__, $userCBIDs);

        Colby::query(
            "DROP TABLE {$tableName}"
        );
    }



    /**
     * @return [string]
     */
    static function CBInstall_requiredClassNames(): array {
        return [
            'CBUsers'
        ];
    }

}

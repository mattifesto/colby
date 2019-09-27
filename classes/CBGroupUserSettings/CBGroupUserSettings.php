<?php

final class CBGroupUserSettings {

    /**
     * @param object $args
     *
     * @return object
     */
    static function CBAjax_fetchSpec(stdClass $args): stdClass {
        $userNumericID = CBModel::valueAsInt($args, 'userNumericID');

        if ($userNumericID === null) {
            throw CBException::createModelIssueException(
                'The "userNumericID" argument is not a valid integer.',
                $args,
                'f205ca27d642fb3c375813e27b27916de8f17893'
            );
        }

        $groupName = CBModel::valueToString($args, 'groupName');

        if (empty($groupName)) {
            throw CBException::createModelIssueException(
                'The "groupName" argument is not a valid group name.',
                $args,
                '6f57ced2e4269a94d7ea52e279ce9edffc870104'
            );
        }

        if (!CBGroupUserSettings::hasAuthorization($groupName)) {
            throw new CBException(
                "You do not have authorization to update user " .
                "membership for the {$groupName} group.",
                '',
                '2686ec76a3a4754e1ee1fd54217414dce37b68f2'
            );
        }

        return (object)[
            'userNumericID' => $userNumericID,
            'groupName' => $groupName,
            'isMember' => ColbyUser::isMemberOfGroup($userNumericID, $groupName),
        ];
    }
    /* CBAjax_fetchSpec() */


    /**
     * @return string
     */
    static function CBAjax_fetchSpec_group(): string {
        return 'Administrators';
    }
    /* CBAjax_fetchSpec_group() */


    /**
     * @return bool
     *
     *      Returns true if the current user has authorization to update user
     *      membership to the group; otherwise false.
     *
     *      True does not necessarily mean the current user has authorization,
     *      but that the authorization should be checked in the calling code.
     *
     *      This function should change over time to use callbacks to group
     *      classes and be definitive.
     */
    static function hasAuthorization($groupName) {
        if (
            $groupName === "Administrators" ||
            $groupName === "Developers"
        ) {
            return ColbyUser::currentUserIsMemberOfGroup('Developers');
        }

        return true;
    }
    /* hasAuthorization() */


    /**
     * @NOTE: 2016_01_06
     *
     *      This acts as if it is a user settings class but it isn't because it
     *      requires one more parameter. It's not a big deal at the moment but
     *      May need to be reconsidered when I have more time.
     *
     * @param object $userData
     * @param string $groupName
     *
     * @return null
     */
    static function renderUserSettings($userData, $groupName) {
        ?>

        <div
            class="CBGroupUserSettings"
            data-user-numeric-id="<?= $userData->id ?>"
            data-group-name="<?= cbhtml($groupName) ?>"
        >
        </div>

        <?php
    }


    /**
     * @return [string]
     */
    static function CBHTMLOutput_JavaScriptURLs() {
        return [
            Colby::flexpath(__CLASS__, 'v529.js', cbsysurl()),
        ];
    }


    /**
     * @return [string]
     */
    static function CBHTMLOutput_requiredClassNames() {
        return [
            'CBErrorHandler',
            'CBUI',
            'CBUIBooleanEditor',
            'Colby',
        ];
    }


    /**
     * @param object $args
     *
     * @return void
     */
    static function CBAjax_updateGroupMembership(stdClass $args): void {
        $groupName = CBModel::valueToString($args, 'groupName');

        if (empty($groupName)) {
            throw CBException::createModelIssueException(
                'The arguments object "groupName" property value of "' .
                $groupName .
                '" is not a valid group name',
                $args,
                'dd3d319d26c705e9b54cc93928754df5c1eb5be6'
            );
        }

        $userNumericID = CBModel::valueAsInt($args, 'userNumericID');

        if ($userNumericID === null) {
            throw CBException::createModelIssueException(
                'The arguments object "userNumericID" property value of ' .
                'null is not a valid numeric user ID',
                $args,
                'a9429e5bdb1f8fc052a6e62f63b3ce2a88c1cebf'
            );
        }

        $isMember = CBModel::valueToBool($args, 'isMember');

        if (!CBGroupUserSettings::hasAuthorization($groupName)) {
            throw new CBException(
                "You do not have authorization to update user membership " .
                "for the {$groupName} group.",
                '',
                '0a4ee1f2d160be3acabae8aa5c6f387c4f0f8e7d'
            );
        }

        ColbyUser::updateGroupMembership($userNumericID, $groupName, $isMember);
    }
    /* CBAjax_updateGroupMembership() */


    /**
     * @return string
     */
    static function CBAjax_updateGroupMembership_group(): string {
        return 'Administrators';
    }
    /* CBAjax_updateGroupMembership_group() */
}

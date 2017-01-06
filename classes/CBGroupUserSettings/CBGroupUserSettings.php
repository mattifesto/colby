<?php

final class CBGroupUserSettings {

    /**
     * @return null
     */
    static function fetchSpecForAjax() {
        $response = new CBAjaxResponse();
        $userID = $_POST['userID'];
        $groupName = $_POST['groupName'];

        if (CBGroupUserSettings::hasAuthorization($groupName)) {
            $response->spec = (object)[
                'userID' => $userID,
                'groupName' => $groupName,
                'isMember' => ColbyUser::isMemberOfGroup($userID, $groupName),
            ];
        } else {
            $response->message = <<<EOT

                You do not have authorization to update user membership for
                the {$groupName} group.

EOT;
        }

        $response->wasSuccessful = true;
        $response->send();
    }

    /**
     * @return stdClass
     */
    static function fetchSpecForAjaxPermissions() {
        return (object)['group' => 'Administrators'];
    }

    /**
     * @return bool
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
        if ($groupName === "Administrators" || $groupName === "Developers") {
            return ColbyUser::isMemberOfGroup(ColbyUser::currentUserId(), "Developers");
        }

        return true;
    }

    /**
     * NOTE: 2016.01.06
     * This acts as if it is a user settings class but it isn't because it
     * requires one more parameter. It's not a big deal at the moment but May
     * need to be reconsidered when I have more time.
     *
     * @param stdClass $userData
     * @param string $groupName
     *
     * @return null
     */
    static function renderUserSettings($userData, $groupName) {
        ?>

        <div class="CBGroupUserSettings" data-userid="<?= $userData->id ?>" data-groupname="<?= cbhtml($groupName) ?>">
        </div>

        <?php
    }

    /**
     * @return [string]
     */
    static function requiredClassNames() {
        return ['CBUI', 'CBUIBooleanEditor'];
    }

    /**
     * @return [string]
     */
    static function requiredJavaScriptURLs() {
        return [Colby::flexnameForJavaScriptForClass(CBSystemURL, __CLASS__)];
    }

    /**
     * @return null
     */
    static function updateGroupMembershipForAjax() {
        $response = new CBAjaxResponse();
        $spec = json_decode($_POST['specAsJSON']);

        if (!CBGroupUserSettings::hasAuthorization($spec->groupName)) {
            $message = <<<EOT

                You do not have authorization to update user membership for
                the {$spec->groupName} group.

EOT;

            throw new RuntimeException($message);
        }

        ColbyUser::updateGroupMembership($spec->userID, $spec->groupName, $spec->isMember);

        $response->wasSuccessful = true;
        $response->send();
    }

    /**
     * @return stdClass
     */
    static function updateGroupMembershipForAjaxPermissions() {
        return (object)['group' => 'Administrators'];
    }
}

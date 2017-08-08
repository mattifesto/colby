<?php

final class CBUser {

    /**
     * @param object $spec
     *
     * @return object|null
     */
    static function CBModel_toModel(stdClass $spec) {
        $userID = CBModel::value($spec, 'userID', null, 'intval');

        if (empty($userID)) {
            CBLog::addMessage(__METHOD__, 4, "The spec `userID` property is empty.");
            return null;
        }

        return (object)[
            'className' => __CLASS__,
            'description' => CBModel::value($spec, 'description', '', 'trim'),
            'facebook' => clone CBModel::valueAsObject($spec, 'facebook'),
            'lastLoggedIn' => CBModel::value($spec, 'lastLoggedIn', 0, 'intval'),
            'title' => CBModel::value($spec, 'title', '', 'trim'),
            'userID' => $userID,
        ];
    }
}

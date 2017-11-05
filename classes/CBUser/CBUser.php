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
            $message = __METHOD__ .
                '() returned null because the spec has no `userID` value.' .
                "\n\n" .
                'spec: ' .
                json_encode($spec, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES);

            CBLog::log((object)[
                'className' => __CLASS__,
                'message' => $message,
                'severity' => 4,
            ]);

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

<?php

final class CBUser {

    /**
     * @param stdClass $spec
     *
     * @return stdClass
     */
    static function specToModel(stdClass $spec) {
        return (object)[
            'className' => __CLASS__,
            'description' => CBModel::value($spec, 'description', '', 'trim'),
            'facebook' => clone $spec->facebook,
            'lastLoggedIn' => intval($spec->lastLoggedIn),
            'title' => CBModel::value($spec, 'title', '', 'trim'),
            'userID' => intval($spec->userID),
        ];
    }
}

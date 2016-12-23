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
            'facebook' => clone $spec->facebook,
            'lastLoggedIn' => intval($spec->lastLoggedIn),
            'userID' => intval($spec->userID),
        ];
    }
}

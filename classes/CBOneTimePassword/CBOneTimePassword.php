<?php

final class CBOneTimePassword {

    /**
     * @return string
     */
    static function generate(): string {
        $oneTimePassword = '';

        for ($index = 0; $index < 6; $index += 1) {
            $oneTimePassword .= random_int(0, 9);
        }

        return $oneTimePassword;
    }
    /* generate() */

}

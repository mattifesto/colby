<?php

final class CBStyleSheet {

    /**
     * @return [string]
     */
    static function CBHTMLOutput_CSSURLs(): array {
        return [
            'https://fonts.googleapis.com/css?family=Open+Sans:400',
            'https://fonts.googleapis.com/css?family=Open+Sans:400italic',
            'https://fonts.googleapis.com/css?family=Open+Sans:600',
            Colby::flexpath(__CLASS__, 'css', cbsysurl()),
        ];
    }
}

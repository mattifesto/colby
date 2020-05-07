<?php

final class CBYouTubeViewEditor {

    /* -- CBAjax interfaces -- -- -- -- -- */



    /**
     * @NOTE 2020_05_06
     *
     *      This function is currently at its first draft. It needs to also
     *      check that:
     *
     *          this is for the www.youtube.com domain
     *
     *          translate short youtube urls
     *
     *          verify non URL video IDs
     *
     * @param object $args
     *
     *      {
     *          suggestedValue: string
     *      }
     *
     * @return object
     *
     *      {
     *          isValid: bool
     *          videoID: string
     *      }
     */
    static function CBAjax_checkVideoID(
        $args
    ): object {
        $suggestedValue = trim(
            CBModel::valueToString(
                $args,
                'suggestedValue'
            )
        );

        $suggestedValueIsURL = (
            substr($suggestedValue, 0, 4) === 'http'
        );

        if ($suggestedValueIsURL) {
            $query = parse_url(
                $suggestedValue,
                PHP_URL_QUERY
            );

            parse_str(
                $query,
                $variables
            );

            if (isset($variables['v'])) {
                $videoID = $variables['v'];
            } else {
                return (object)[
                    'isValid' => false,
                ];

            }
        } else {
            $videoID = $suggestedValue;
        }

        return (object)[
            'isValid' => true,
            'videoID' => $videoID,
        ];
    }
    /* CBAjax_checkVideoID() */



    /**
     * @return string
     */
    static function CBAjax_checkVideoID_getUserGroupClassName(): string {
        return 'CBAdministratorsUserGroup';
    }



    /* -- CBHTMLOutput interfaces -- -- -- -- -- */



    /**
     * @return [string]
     */
    static function CBHTMLOutput_JavaScriptURLs(): array {
        return [
            Colby::flexpath(__CLASS__, 'v614.js', cbsysurl()),
        ];
    }



    /**
     * @return [string]
     */
    static function CBHTMLOutput_requiredClassNames(): array {
        return [
            'CBAjax',
            'CBModel',
            'CBUI',
            'CBUIPanel',
            'CBUISelector',
            'CBUIStringEditor',
        ];
    }
    /* CBHTMLOutput_requiredClassNames() */

}

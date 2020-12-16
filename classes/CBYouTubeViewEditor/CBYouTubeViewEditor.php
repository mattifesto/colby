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
            $originalURL = $suggestedValue;
        } else {
            $originalURL = "https://www.youtube.com/watch?v={$suggestedValue}";
        }

        try {
            $redirectURL = CBCurl::fetchRedirectURL(
                $originalURL
            );
        } catch (Throwable $error) {
            return (object)[
                'isValid' => false,
            ];
        }

        $query = parse_url(
            $redirectURL,
            PHP_URL_QUERY
        );

        parse_str(
            $query,
            $variables
        );

        if (!isset($variables['v'])) {
            return (object)[
                'isValid' => false,
            ];
        }

        $videoID = $variables['v'];

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
    static function
    CBHTMLOutput_JavaScriptURLs(
    ): array {
        return [
            Colby::flexpath(
                __CLASS__,
                'v675.5.js',
                cbsysurl()
            ),
        ];
    }
    /* CBHTMLOutput_JavaScriptURLs() */



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
            'CBUIStringEditor2',
        ];
    }
    /* CBHTMLOutput_requiredClassNames() */

}

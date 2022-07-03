<?php

final class
CB_YouTube {

    // -- CBCodeAdmin interfaces



    /**
     * @return object
     */
    static function
    CBCodeAdmin_searches(
    ): stdClass
    {
        $codeSearchSpec =
        CBModel::createSpec(
            'CBCodeSearch',
            '8f3b4f6847045f6202d16f92dfd047840b75ea52'
        );

        CBCodeSearch::setNoticeVersion(
            $codeSearchSpec,
            '2022_07_02_1656783035'
        );

        CBCodeSearch::setWarningVersion(
            $codeSearchSpec,
            '2022_07_02_1656783036'
        );

        $codeSearchSpec->cbmessage =
        <<<EOT

            YouTube channel data is now stored in CB_YouTubeChannel models.

        EOT;

        $codeSearchSpec->regex =
        '\bCB_YouTube::fetchCredentials\b';

        $codeSearchSpec->severity =
        4;

        $codeSearchSpec->title =
        'CB_YouTube::fetchCredentials()';

        return $codeSearchSpec;
    }
    // CBCodeAdmin_searches()



    /* -- functions -- */



    /**
     * @param string $functionName
     *
     *      examples:
     *          'activites'
     *          'captions'
     *          'channelbanners'
     *          'channels'
     *
     * @param object $arguments
     *
     *      properties:
     *          'part' => <string>
     *          'id' => YouTube channel ID
     *          'key' => YouTube api key
     *
     */
    static function
    call(
        string $functionName,
        stdClass $arguments
    ): stdClass {
        $functionNameAsQueryString = urlencode(
            $functionName
        );

        $argumentNames = array_keys(
                get_object_vars(
                $arguments
            )
        );

        $queryStringVariables = array_map(
            function (
                $argumentName
            ) use (
                $arguments
            ) {
                $queryStringVariable = urlencode(
                    $argumentName
                );

                $queryStringVariable .= '=';

                $queryStringVariable .= urlencode(
                    CBModel::valueToString(
                        $arguments,
                        $argumentName
                    )
                );

                return $queryStringVariable;
            },
            $argumentNames
        );

        $queryStringVariables = implode(
            '&',
            $queryStringVariables
        );

        $originalURL = (
            'https://youtube.googleapis.com' .
            '/youtube/v3/' .
            $functionNameAsQueryString .
            '?' .
            $queryStringVariables
        );

        $curlHandle = curl_init(
            $originalURL
        );

        curl_setopt(
            $curlHandle,
            CURLOPT_FOLLOWLOCATION,
            true
        );

        curl_setopt(
            $curlHandle,
            CURLOPT_RETURNTRANSFER,
            true
        );

        curl_setopt(
            $curlHandle,
            CURLOPT_SSH_COMPRESSION,
            true
        );

        $responseAsJSON = curl_exec(
            $curlHandle
        );

        $errorNumber = curl_errno(
            $curlHandle
        );

        if ($errorNumber !== 0) {
            $errorMessage = curl_error(
                $curlHandle
            );

            throw new Exception(
                "cURL error: {$errorMessage} ({$errorNumber})"
            );
        }

        $httpCode = curl_getinfo(
            $curlHandle,
            CURLINFO_HTTP_CODE
        );

        if ($httpCode !== 200) {
            throw new Exception(
                "cURL HTTP code: {$httpCode}"
            );
        }

        curl_close(
            $curlHandle
        );

        return json_decode(
            $responseAsJSON
        );
    }
    /* call() */



    /**
     * This function fetches a list of recent uploads for a YouTube channel.
     *
     * @deprecated 2022_07_03
     *
     *      Use CB_YouTube::fetchRecentUploads2()
     *
     * @return [object]|null
     */
    static function
    fetchRecentUploads(
    ): ?array
    {
        $youtubeCredentials =
        CB_YouTube::fetchCredentials();

        if (
            $youtubeCredentials === null
        ) {
            return null;
        }

        $returnValue =
        CB_YouTube::fetchRecentUploads2(
            $youtubeCredentials->youtubeAPIKey,
            $youtubeCredentials->youtubeChannelID
        );

        return $returnValue;
    }
    /* fetchRecentUploads() */



    /**
     * This function fetches a list of recent uploads for a YouTube channel.
     *
     * @param string $googleAPIKey
     * @param string $youtubeChannelID
     *
     * @return [object]|null
     */
    static function
    fetchRecentUploads2(
        string $googleAPIKey,
        string $youtubeChannelID
    ): ?array
    {
        $response =
        CB_YouTube::call(
            'channels',
            (object)
            [
                'id' =>
                $youtubeChannelID,

                'key' =>
                $googleAPIKey,

                'part' =>
                'contentDetails',
            ]
        );

        $uploadsPlaylistID =
        CBModel::valueToString(
            $response,
            'items.[0].contentDetails.relatedPlaylists.uploads'
        );

        $response =
        CB_YouTube::call(
            'playlistItems',
            (object)
            [
                'key' =>
                $googleAPIKey,

                'maxResults' =>
                5,

                'part' =>
                'snippet',

                'playlistId' =>
                $uploadsPlaylistID,
            ]
        );

        $items =
        CBModel::valueAsArray(
            $response,
            'items'
        );

        return $items;
    }
    /* fetchRecentUploads2() */



    /**
     * @deprecated 2022_06_13
     *
     *      This function fetches the api key and channel id from the site
     *      preferences model. These are now stored in a CB_YouTubeChannel
     *      model.
     *
     * @return object|null
     */
    static function
    fetchCredentials(
    ): ?stdClass {
        $youtubeAPIKey = CBSitePreferences::getYouTubeAPIKey(
            CBModelCache::fetchModelByID(
                CBSitePreferences::ID()
            )
        );

        if (
            $youtubeAPIKey === ''
        ) {
            return null;
        }

        $youtubeChannelID = CBSitePreferences::getYouTubeChannelID(
            CBModelCache::fetchModelByID(
                CBSitePreferences::ID()
            )
        );

        if (
            $youtubeChannelID === ''
        ) {
            return null;
        }

        return (object)[
            'youtubeAPIKey' => $youtubeAPIKey,
            'youtubeChannelID' => $youtubeChannelID,
        ];
    }
    /* fetchYouTubeCredentials() */



    /**
     * @param string $youtubeAPIKey
     * @param string $youtubeChannelID
     *
     * @return object
     */
    static function
    fetchStatistics(
        string $youtubeAPIKey,
        string $youtubeChannelID
    ): stdClass
    {
        $statistics =
        CB_YouTube::call(
            'channels',
            (object)
            [
                'id' =>
                $youtubeChannelID,

                'key' =>
                $youtubeAPIKey,

                'part' =>
                'statistics',
            ]
        );

        return $statistics;
    }
    // fetchStatistics()

}

<?php

final class
CB_YouTube {

    /* -- functions -- */



    /**
     * @param string $functionName
     * @param object $arguments
     *
     * @return object
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
     * @return [object]|null
     */
    static function
    fetchRecentUploads(
    ): ?array {
        $youtubeCredentials = CB_YouTube::fetchCredentials();

        if (
            $youtubeCredentials === null
        ) {
            return null;
        }

        $response = CB_YouTube::call(
            'channels',
            (object)[
                'id' => $youtubeCredentials->youtubeChannelID,
                'key' => $youtubeCredentials->youtubeAPIKey,
                'part' => 'contentDetails',
            ]
        );

        $uploadsPlaylistID = CBModel::valueToString(
            $response,
            'items.[0].contentDetails.relatedPlaylists.uploads'
        );

        $response = CB_YouTube::call(
            'playlistItems',
            (object)[
                'key' => $youtubeCredentials->youtubeAPIKey,
                'maxResults' => 5,
                'part' => 'snippet',
                'playlistId' => $uploadsPlaylistID,
            ]
        );

        $items = CBModel::valueAsArray(
            $response,
            'items'
        );

        return $items;
    }
    /* fetchRecentUploads() */



    /**
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

}

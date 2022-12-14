<?php

final class
CB_SearchResult
{
    /* -- accessors -- */



    /**
     * @param object $searchResultModel
     *
     * @return object|null
     */
    static function
    getPrimaryImageModel(
        stdClass $searchResultModelArgument
    ): ?stdClass
    {
        $primaryImageModel =
        CBModel::valueAsModel(
            $searchResultModelArgument,
            'CB_SearchResult_primaryImageModel_property'
        );

        return $primaryImageModel;
    }
    /* getPrimaryImageModel() */



    /**
     * @param object $searchResultModel
     * @param string $newTitle
     *
     * @return void
     */
    static function
    setPrimaryImageModel(
        stdClass $searchResultModelArgument,
        ?stdClass $newPrimaryImageModelArgument
    ): void
    {
        $searchResultModelArgument->CB_SearchResult_primaryImageModel_property =
        $newPrimaryImageModelArgument;
    }
    /* setPrimaryImageModel() */



    /**
     * @param object $searchResultModel
     *
     * @return string
     */
    static function
    getTitle(
        stdClass $searchResultModel
    ): string {
        return CBModel::valueToString(
            $searchResultModel,
            'CB_SearchResult_title_property'
        );
    }
    /* getTitle() */



    /**
     * @param object $searchResultModel
     * @param string $newTitle
     *
     * @return void
     */
    static function
    setTitle(
        stdClass $searchResultModel,
        string $newTitle
    ): void {
        $searchResultModel->CB_SearchResult_title_property = $newTitle;
    }
    /* setTitle() */



    /**
     * @param object $searchResultModel
     *
     * @return string
     */
    static function
    getURL(
        stdClass $searchResultModel
    ): string {
        return CBModel::valueToString(
            $searchResultModel,
            'CB_SearchResult_URL_property'
        );
    }
    /* getURL() */



    /**
     * @param object $searchResultModel
     * @param string $newURL
     *
     * @return void
     */
    static function
    setURL(
        stdClass $searchResultModel,
        string $newURL
    ): void {
        $searchResultModel->CB_SearchResult_URL_property = $newURL;
    }
    /* setURL() */

}

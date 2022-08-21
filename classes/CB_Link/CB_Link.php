<?php

final class
CB_Link
{
    // -- CBHTMLOutput interfaces



    /**
     * @return [string]
     */
    static function
    CBHTMLOutput_JavaScriptURLs(
    ): array
    {
        $javaScriptURLs =
        [
            CBLibrary::buildLibraryClassFilePath(
                __CLASS__,
                '2022_08_20_1661013494',
                'js',
                cbsysurl()
            ),
        ];

        return $javaScriptURLs;
    }
    // CBHTMLOutput_JavaScriptURLs()



    /**
     * @return [string]
     */
    static function
    CBHTMLOutput_requiredClassNames(
    ): array
    {
        $requiredClassNames =
        [
            'CBException',
            'CBModel',
        ];

        return $requiredClassNames;
    }
    // CBHTMLOutput_requiredClassNames()



    // -- CBModel interfaces



    /**
     * @param object $linkSpec
     *
     * @return object
     */
    static function
    CBModel_build(
        stdClass $linkSpec
    ): stdClass
    {
        $linkModel =
        (object)[];

        CB_Link::setText(
            $linkModel,
            CB_Link::getText(
                $linkSpec
            )
        );

        CB_Link::setURL(
            $linkModel,
            CB_Link::getURL(
                $linkSpec
            )
        );

        return $linkModel;
    }
    // CBModel_build()



    /**
     * @param object $linkModel
     *
     * @return string
     */
    static function
    CBModel_toSearchText(
        stdClass $linkModel
    ): string
    {
        $searchText =
        CB_Link::getText(
            $linkModel
        ) .
        ' ' .
        CB_Link::getURL(
            $linkModel
        );

        return $searchText;
    }
    // CBModel_toSearchText()



    // accessors



    /**
     * @param object $linkModel
     *
     * @return string
     */
    static function
    getText(
        stdClass $linkModel
    ): string
    {
        $linkText =
        CBModel::valueToString(
            $linkModel,
            'CB_Link_text_property'
        );

        $linkText =
        trim(
            $linkText
        );

        return $linkText;
    }
    // getText()



    /**
     * @param object $linkModel
     * @param string $newText
     *
     * @return void
     */
    static function
    setText(
        stdClass $linkModel,
        string $newText
    ): void
    {
        $linkModel->CB_Link_text_property =
        $newText;
    }
    // setText()



    /**
     * @param object $linkModel
     *
     * @return string
     */
    static function
    getURL(
        stdClass $linkModel
    ): string
    {
        $linkURL =
        CBModel::valueToString(
            $linkModel,
            'CB_Link_url_property'
        );

        return $linkURL;
    }
    // getURL()



    /**
     * @param object $linkModel
     * @param string $newURL
     *
     * @return void
     */
    static function
    setURL(
        stdClass $linkModel,
        string $newURL
    ): void
    {
        $linkModel->CB_Link_url_property =
        $newURL;
    }
    // setURL()

}

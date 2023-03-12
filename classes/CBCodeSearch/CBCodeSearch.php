<?php

/**
 * @NOTE 2022_06_19
 *
 *      Specs of this class we originally created as objects and not really
 *      considered to be specs which of course led to ill-defined and poorly
 *      documented code.
 *
 *      This class is becoming an actual model and is continually being updated
 *      at this time.
 */
final class
CBCodeSearch
{
    /**
     * @NOTE 2022_06_03
     *
     *      Properties:
     *
     *      CBCodeSearch_errorVersion_property
     *      CBCodeSearch_noticeVersion_property
     *      CBCodeSearch_warningVersion_property
     *
     *      CBCodeSearch_ackArguments_property
     *
     *          additional arguments to ack such as
     *
     *          '--ignore-file=is:CBCodeSearch.php'
     *
     *      severity
     *      title
     *
     *      deprecated:
     *
     *      args -> CBCodeSearch_ackArguments_property
     *      CBCodeSearch_CBID -> model CBID
     *      cbmessage -> CBCodeSearch_cbmessage_property
     *      errorStartDate -> CBCodeSearch_errorVersion_property
     *      filetype -> CBCodeSearch_fileType_property
     *      noticeStartDate -> CBCodeSearch_noticeVersion_property
     *      noticeVersion -> CBCodeSearch_noticeVersion_property
     *      regex -> CBCodeSearch_regularExpression_property
     *      warningStartDate -> CBCodeSearch_warningVersion_property
     */



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
                '2022_10_08_1665246592',
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
            'CBModel',
        ];

        return $requiredClassNames;
    }
    // CBHTMLOutput_requiredClassNames()



    // -- accessors



    /**
     * @param object $codeSearchModelArgument
     *
     * @return void
     */
    static function
    getAckArguments(
        stdClass $codeSearchModelArgument
    ): string
    {
        $ackArguments =
        CBModel::valueToString(
            $codeSearchModelArgument,
            'CBCodeSearch_ackArguments_property'
        );

        if (
            $ackArguments === ''
        ) {
            $ackArguments =
            CBModel::valueToString(
                $codeSearchModelArgument,
                'args'
            );
        }

        return $ackArguments;
    }
    // getAckArguments()



    /**
     * @param object $codeSearchModel
     * @param string $newErrorVersion
     *
     * @return void
     */
    static function
    setAckArguments(
        stdClass $codeSearchModel,
        string $newAckArguments
    ): void
    {
        $codeSearchModel->CBCodeSearch_ackArguments_property =
        $newAckArguments;

        unset(
            $codeSearchModel->args
        );
    }
    // setAckArguments()



    /**
     * @param object $codeSearchModelArgument
     *
     * @return void
     */
    static function
    getCBMessage(
        stdClass $codeSearchModelArgument
    ): string
    {
        if (
            isset(
                $codeSearchModelArgument->CBCodeSearch_cbmessage_property
            )
        ) {
            $cbmessage =
            CBModel::valueToString(
                $codeSearchModelArgument,
                "CBCodeSearch_cbmessage_property"
            );
        }
        // if

        else
        {
            // @deprecated 2022_10_08_1665245776

            $cbmessage =
            CBModel::valueToString(
                $codeSearchModelArgument,
                'cbmessage'
            );
        }

        return $cbmessage;
    }
    // getCBMessage()



    /**
     * @param object $codeSearchModelArgument
     * @param string $cbmessageArgument
     *
     * @return void
     */
    static function
    setCBMessage(
        stdClass $codeSearchModelArgument,
        string $cbmessageArgument
    ): void
    {
        $codeSearchModelArgument->CBCodeSearch_cbmessage_property =
        $cbmessageArgument;

        unset(
            $codeSearchModelArgument->cbmessage
        );
    }
    // setCBMessage()



    /**
     * @param object $codeSearchModel
     * @param string $newErrorVersion
     *
     * @return void
     */
    static function
    setErrorVersion(
        stdClass $codeSearchModel,
        string $newErrorVersion
    ): void
    {
        $codeSearchModel->
        CBCodeSearch_errorVersion_property =
        $newErrorVersion;
    }
    // setErrorVersion()



    /**
     * @param object $codeSearchModelArgument
     *
     * @return string
     */
    static function
    getFileType(
        stdClass $codeSearchModelArgument,
    ): string
    {
        if (
            isset(
                $codeSearchModelArgument->CBCodeSearch_fileType_property
            )
        ) {
            $fileTypePropertyValue =
            CBModel::valueToString(
                $codeSearchModelArgument,
                'CBCodeSearch_fileType_property'
            );
        }

        else
        {
            /**
             * @deprecated 2022_11_03_1667490968
             */

            $fileTypePropertyValue =
            CBModel::valueToString(
                $codeSearchModelArgument,
                'filetype'
            );
        }

        return $fileTypePropertyValue;
    }
    // getFileType()



    /**
     * @param object $codeSearchModelArgument
     * @param string $newFileTypePropertyValueArgument
     *
     * @return void
     */
    static function
    setFileType(
        stdClass $codeSearchModelArgument,
        string $newFileTypePropertyValueArgument
    ): void
    {
        $codeSearchModelArgument->CBCodeSearch_fileType_property =
        $newFileTypePropertyValueArgument;

        unset(
            $codeSearchModelArgument->filetype
        );
    }
    // setFileType()


    /**
     * @param object $codeSearchModel
     * @param string $newNoticeVersion
     *
     * @return void
     */
    static function
    setNoticeVersion(
        stdClass $codeSearchModel,
        string $newNoticeVersion
    ): void
    {
        $codeSearchModel->
        CBCodeSearch_noticeVersion_property =
        $newNoticeVersion;
    }
    // setNoticeVersion()



    /**
     * @param object $codeSearchModelArgument
     *
     * @return void
     */
    static function
    getRegularExpression(
        stdClass $codeSearchModelArgument
    ): string
    {
        $aValueForTheNewPropertyNameHasBeenSet =
        isset(
            $codeSearchModelArgument->CBCodeSearch_regularExpression_property
        );

        if (
            $aValueForTheNewPropertyNameHasBeenSet
        ) {
            $regularExpressionValue =
            CBModel::valueToString(
                $codeSearchModelArgument,
                "CBCodeSearch_regularExpression_property"
            );
        }
        // if

        else
        {
            // @deprecated 2023_03_12_1678648389

            $regularExpressionValue =
            CBModel::valueToString(
                $codeSearchModelArgument,
                'regex'
            );
        }

        return $regularExpressionValue;
    }
    // getRegularExpression()



    /**
     * @param object $codeSearchModelArgument
     * @param string $newRegularExpressionArgument
     *
     * @return void
     */
    static function
    setRegularExpression(
        stdClass $codeSearchModelArgument,
        string $newRegularExpressionArgument
    ): void
    {
        $codeSearchModelArgument->CBCodeSearch_regularExpression_property =
        $newRegularExpressionArgument;

        unset(
            $codeSearchModelArgument->regex
        );
    }
    // setRegularExpression()



    /**
     * @param object $codeSearchModel
     * @param string $newWarningVersion
     *
     * @return void
     */
    static function
    setWarningVersion(
        stdClass $codeSearchModel,
        string $newWarningVersion
    ): void
    {
        $codeSearchModel->
        CBCodeSearch_warningVersion_property =
        $newWarningVersion;
    }
    // setWarningVersion()

}

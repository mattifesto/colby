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
     *      cbmessage
     *      regex
     *      severity
     *      title
     *
     *      deprecated:
     *
     *      args -> CBCodeSearch_ackArguments_property
     *      CBCodeSearch_CBID -> model CBID
     *      errorStartDate -> CBCodeSearch_errorVersion_property
     *      noticeStartDate -> CBCodeSearch_noticeVersion_property
     *      noticeVersion -> CBCodeSearch_noticeVersion_property
     *      warningStartDate -> CBCodeSearch_warningVersion_property
     */


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

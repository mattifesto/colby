<?php

/**
 * @NOTE 2022_08_25
 *
 *      The create Ajax function currenty exists in the CB_Moment class but is
 *      moving to this class. While that transition is in progress new
 *      supporting code will be added to this class.
 */
final class
CB_Ajax_Moment_Create
{
    // -- functions



    /**
     * @param object $executorArguments
     *
     * @return string
     *
     *      An empty string will be returned if no valid text has been provided.
     */
    static function
    getTextArgument(
        stdClass $executorArguments
    ): string
    {
        $textArgument =
        trim(
            CBModel::valueToString(
                $executorArguments,
                'CB_Moment_create_text_parameter'
            )
        );

        return $textArgument;
    }
    // getTextArgument()



    /**
     * This function retrieves and verifies the image spec from the executor
     * arguments.
     *
     * @param object $executorArguments
     *
     * @return <CBImage spec>|null
     */
    static function
    getVerifiedImageSpecArgument(
        stdClass $executorArguments
    ): ?stdClass
    {
        $imageSpecArgument =
        CBModel::valueAsModel(
            $executorArguments,
            'CB_Moment_create_imageModel_parameter',
            'CBImage'
        );

        if (
            $imageSpecArgument ===
            null
        ) {
            return null;
        }

        $imageSpecArgumentCBID =
        CBModel::getCBID(
            $imageSpecArgument
        );

        $verifiedImageSpec =
        CBModels::fetchSpecByCBID(
            $imageSpecArgumentCBID
        );

        $verifiedImageSpecClassName =
        CBModel::getClassName(
            $verifiedImageSpec
        );

        if (
            $verifiedImageSpec ===
            null ||
            $verifiedImageSpecClassName !==
            'CBImage'
        ) {
            throw new CBExceptionWithValue(
                'CB_Moment_create_imageModel_parameter is not valid',
                $imageSpecArgument,
                'f0a3a15573f7e32fb22a3d46c661d9552669dc31'
            );
        }

        return $verifiedImageSpec;
    }
    // getVerifiedImageSpecArgument

}

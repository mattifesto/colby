<?php

final class
CB_Tag
{
    private static
    $cachedAssociatedImageModelsByCanonicalTagName =
    [];



    // -- CBModel interfaces



    /**
     * @param object $tagSpec
     *
     * @return object
     */
    static function
    CBModel_build(
        stdClass $tagSpec
    ): stdClass
    {
        $tagModel =
        (object)[];

        $canonicalTagName =
        CB_Tag::getCanonicalTagName(
            $tagSpec
        );

        if (
            $canonicalTagName === null
        ) {
            throw new CBExceptionWithValue(
                'This CB_Tag spec does not have a valid canonical tag name.',
                $tagSpec,
                'a9e779de39d2cacd57f76ffd3e93656874a3e18d'
            );
        }

        CB_Tag::setCanonicalTagName(
            $tagModel,
            $canonicalTagName
        );

        $tagModelCBID =
        CBModel::getCBID(
            $tagSpec
        );

        if (
            $tagModelCBID !== null
        ) {
            $expectedTagModelCBID =
            CB_Tag::convertCanonicalTagNameToTagModelCBID(
                $canonicalTagName
            );

            if (
                $tagModelCBID !== $expectedTagModelCBID
            ) {
                throw new CBExceptionWithValue(
                    CBConvert::stringToCleanLine(<<<EOT

                        This tag spec can't be built because it has a model CBID
                        of ${tagModelCBID} instead of ${expectedTagModelCBID}.

                    EOT),
                    $tagSpec,
                    '7ab51e96759c49aa0d5d74c6c4358365e6a381cc'
                );
            }
        }

        return
        $tagModel;
    }
    // CBModel_build()



    /**
     * @param object $tagModel
     *
     * @return string
     */
    static function
    CBModel_getTitle(
        stdClass $tagModel
    ): string
    {
        $canonicalTagName =
        CB_Tag::getCanonicalTagName(
            $tagModel
        );

        return
        "#${canonicalTagName}";
    }
    // CBModel_getTitle()



    /**
     * @param object $tagModel
     *
     * @return string
     */
    static function
    CBModel_toSearchText(
        stdClass $tagModel
    ): string
    {
        $canonicalTagName =
        CB_Tag::getCanonicalTagName(
            $tagModel
        );

        return
        "#${canonicalTagName}";
    }
    // CBModel_toSearchText()



    /**
     * @param object $upgradableTagSpec
     *
     * @return object
     */
    static function
    CBModel_upgrade(
        stdClass $upgradableTagSpec
    ): stdClass
    {
        $upgradableTagSpec->
        CB_Tag_buildProcessVersion =
        '2022.05.16.1652666887';

        return
        $upgradableTagSpec;
    }
    // CBModel_upgrade()



    // -- accessors



    /**
     * @param object $tagModel
     *
     * @return string
     */
    static function
    getCanonicalTagName(
        stdClass $tagModel
    ): string
    {
        return
        CBModel::valueToString(
            $tagModel,
            'CB_Tag_canonicalTagName_property'
        );
    }
    // getCanonicalTagName()



    /**
     * @param object $tagModel
     * @param string $potentialCanonicalTagName
     *
     * @return void
     */
    static function
    setCanonicalTagName(
        stdClass $tagModel,
        string $potentialCanonicalTagName
    ): void
    {
        $prettyCanonicalTagName =
        CB_URL::convertRawStringToPrettyWord(
            $potentialCanonicalTagName
        );

        $canonicalTagName =
        mb_strtolower(
            $prettyCanonicalTagName
        );

        if (
            $canonicalTagName !== $potentialCanonicalTagName
        ) {
            throw new CBExceptionWithValue(
                CBConvert::stringToCleanLine(<<<EOT

                    The potential canonical tag name
                    "${$potentialCanonicalTagName}" is not a valid canonical tag
                    name.

                EOT),
                $prettyCanonicalTagName,
                '68074865888349a7613e4efc6b3c03da83916854'
            );
        }

        $tagModel->CB_Tag_canonicalTagName_property =
        $potentialCanonicalTagName;
    }
    // setCanonicalTagName()



    // -- functions



    /**
     * @param string $potentialCanonicalTagName
     *
     * @return CBID
     */
    static function
    convertCanonicalTagNameToTagModelCBID(
        string $potentialCanonicalTagName
    ): string
    {
        $canonicalTagName =
        CB_URL::convertRawStringToPrettyWord(
            $potentialCanonicalTagName
        );

        $canonicalTagName =
        mb_strtolower(
            $canonicalTagName
        );

        if (
            $potentialCanonicalTagName !== $canonicalTagName ||
            strlen($canonicalTagName) === 0
        ) {
            $method = __METHOD__ . "()";

            throw new CBExceptionWithValue(
                CBConvert::stringToCleanLine(<<<EOT

                    The potential canonical tag name
                    "${potentialCanonicalTagName}" provided to ${method} is not
                    valid.

                EOT),
                $potentialCanonicalTagName,
                '5e6b511bc6269cd6d94688761f6d789056136ac4'
            );
        }

        return
        sha1(
            "7a43ac6588095ef557148896347962aa82a7233f ${canonicalTagName}"
        );
    }
    // convertCanonicalTagNameToTagModelCBID()



    /**
     * @param string $potentialPrettyTagName
     *
     * @return string
     */
    static function
    convertPrettyTagNameToCanonicalTagName(
        string $potentialPrettyTagName
    ): string
    {
        $potentialPrettyTagNameIsValid =
        CB_URL::potentialPrettyWordIsValid(
            $potentialPrettyTagName
        );

        if (
            $potentialPrettyTagNameIsValid !== true
        ) {
            throw new CBExceptionWithValue(
                CBConvert::stringToCleanLine(<<<EOT

                    The string "${$potentialPrettyTagNameIsValid}" is not a
                    pretty tag name.

                EOT),
                $prettyTagNames,
                'c65d5d0c1c6dbb005ba51bbc0b43644c22a41e9a'
            );
        }

        $canonicalTagName =
        mb_strtolower(
            $potentialPrettyTagName
        );

        return
        $canonicalTagName;
    }
    // convertPrettyTagNameToCanonicalTagName()



    /**
     * This function will create or verify tag models exist for the pretty tag
     * names passed in.
     *
     * @param string $prettyTagName
     */
    static function
    createTagModelForPrettyTagName(
        string $potentialPrettyTagName
    ): void
    {
        $potentialPrettyTagNameIsValid =
        CB_URL::potentialPrettyWordIsValid(
            $potentialPrettyTagName
        );

        if (
            $potentialPrettyTagNameIsValid !== true
        ) {
            throw new CBExceptionWithValue(
                CBConvert::stringToCleanLine(<<<EOT

                    The string "${potentialPrettyTagName}" is not a valid pretty
                    tag name.

                EOT),
                $potentialPrettyTagName,
                'd74259df164f1b56cff9ddb8b2f11b9e720cd675'
            );
        }

        $canonicalTagName =
        mb_strtolower(
            $potentialPrettyTagName
        );

        $tagModelCBID =
        CB_Tag::convertCanonicalTagNameToTagModelCBID(
            $canonicalTagName
        );

        $tagModel =
        CBModels::fetchModelByCBID(
            $tagModelCBID
        );

        if (
            $tagModel === null
        ) {
            $tagSpec =
            CBModel::createSpec(
                'CB_Tag',
                $tagModelCBID
            );

            CB_Tag::setCanonicalTagName(
                $tagSpec,
                $canonicalTagName
            );

            CBModels::save(
                $tagSpec
            );
        }
    }
    // createTagModelForPrettyTagName()



    /**
     * @param string $prettyTagName
     *
     * @return object|null
     */
    static function
    fetchAndCacheAssociatedImageModelByPrettyTagName(
        string $prettyTagName
    ): ?stdClass
    {
        $canonicalTagName =
        CB_Tag::convertPrettyTagNameToCanonicalTagName(
            $prettyTagName
        );

        $hasCachedAssociatedImageModel =
        array_key_exists(
            $canonicalTagName,
            CB_Tag::$cachedAssociatedImageModelsByCanonicalTagName
        );

        if (
            $hasCachedAssociatedImageModel
        ) {
            return
            CB_Tag::$cachedAssociatedImageModelsByCanonicalTagName[
                $canonicalTagName
            ];
        }

        $tagModelCBID =
        CB_Tag::convertCanonicalTagNameToTagModelCBID(
            $canonicalTagName
        );

        $associatedImageModel =
        CBModelToCBImageAssociation::fetchAssociatedImageModelByModelCBID(
            $tagModelCBID
        );

        CB_Tag::$cachedAssociatedImageModelsByCanonicalTagName[
            $canonicalTagName
        ] =
        $associatedImageModel;

        return
        $associatedImageModel;
    }
    // fetchAndCacheAssociatedImageModelByPrettyTagName()

}

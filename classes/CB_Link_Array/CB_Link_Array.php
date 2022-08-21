<?php

/**
 * This class represents an array of models as a model which is easily editable.
 *
 * @TODO 2022_08_21_1661055095
 *
 *      Replace this class with a model that holds a generic array of models.
 *      That models can also have a property that holds the class name(s) of the
 *      models that are allowed to be in the array. This model will be used to
 *      hold all arrays of models.
 *
 *      There are classes (such as views that have child views) that hold raw
 *      arrays of models that should use this model to hold them instead.
 */
final class
CB_Link_Array
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
                '2022_08_20_1660963606',
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



    // CBModel interfaces



    /**
     * @param object $linkSpec
     *
     * @return object
     */
    static function
    CBModel_build(
        stdClass $linkArraySpec
    ): stdClass
    {
        $linkArrayModel =
        (object)[];

        CB_Link_Array::setLinks(
            $linkArrayModel,
            CB_Link_Array::getLinks(
                $linkArraySpec
            )
        );

        return $linkArrayModel;
    }
    // CBModel_build()



    // accessors



    /**
     * @param object $linkArrayModel
     *
     * @return [<CB_Link model>]
     */
    static function
    getLinks(
        stdClass $linkArrayModel
    ): array
    {
        $links =
        CBModel::valueToArray(
            $linkArrayModel,
            'CB_Link_Array_links_property'
        );

        return $links;
    }
    // getLinks()



    /**
     * @param object $linkArrayModel
     * @param [<CB_Link model>] $newLinks
     *
     * @return void
     */
    static function
    setLinks(
        stdClass $linkArrayModel,
        array $newLinks
    ): void
    {
        $linkArrayIsValid =
        CB_Link::isLinkArrayValid(
            $newLinks
        );

        if (
            $linkArrayIsValid !==
            true
        ) {
            throw new CBExceptionWithValue(
                'The new links are not valid.',
                $newLinks,
                'ce5c9f062033c47a7ca7c8297b5ce77ae65ae403'
            );
        }

        $linkArrayModel->CB_Link_Array_links_property =
        $newLinks;
    }
    // setLinks()



    // functions



    /**
     * @param [<???>] $linkModels
     *
     * @return bool
     */
    static function
    isLinkArrayValid(
        array $linkModels
    ): bool
    {
        foreach(
            $linkModels as
            $linkModel
        ) {
            $className =
            CBModel::getClassName(
                $linkModel
            );

            if (
                $className !==
                'CB_Link'
            ) {
                return false;
            }
        }

        return true;
    }
    // isLinkArrayValid()

}

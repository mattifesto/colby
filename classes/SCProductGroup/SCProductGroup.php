<?php

final class
SCProductGroup {

    /* -- CBModel interfaces -- -- -- -- -- */

    /**
     * @param object $spec
     *
     * @return object
     */
    static function CBModel_build(stdClass $spec): stdClass {
        $productGroupName = CBModel::valueAsName($spec, 'name');

        if ($productGroupName === null) {
            throw CBException::createModelIssueException(
                'This spec has an invalid "name" property value.',
                $spec,
                '692219491ef7bdf29f798d0ff35bd19cd6a2a775'
            );
        }

        return (object)[
            'hasPage' => CBModel::valueToBool(
                $spec,
                'hasPage'
            ),

            'name' => $productGroupName,

            'title' => trim(
                CBModel::valueToString(
                    $spec,
                    'title'
                )
            ),
        ];
    }
    /* CBModel_build() */


    /**
     * @param object $spec
     *
     * @return string
     */
    static function CBModel_toID(stdClass $spec): string {
        $productGroupName = CBModel::valueAsName($spec, 'name');

        if ($productGroupName === null) {
            throw CBException::createModelIssueException(
                'This spec has an invalid "name" property value.',
                $spec,
                '33f4bbca9b0443a1858f790f76f1a70757f2b19b'
            );
        }

        return SCProductGroup::productGroupNameToProductGroupID(
            $productGroupName
        );
    }
    /* CBModel_toID() */


    /* -- CBModels interfaces -- -- -- -- -- */

    /**
     * @param [string] $IDs
     *
     * @return void
     */
    static function CBModels_willDelete($IDs): void {
        CBTasks2::restart(
            'SCProductGroupUpdateTask',
            $IDs,
            SCProductGroupUpdateTask::getDefaultPriority()
        );
    }
    /* CBModels_willDelete() */


    /**
     * @param [object] $models
     *
     * @return void
     */
    static function CBModels_willSave(array $models): void {
        $IDs = array_map(
            function ($model) {
                return $model->ID;
            },
            $models
        );

        CBTasks2::restart(
            'SCProductGroupUpdateTask',
            $IDs,
            SCProductGroupUpdateTask::getDefaultPriority()
        );
    }
    /* CBModels_willSave() */


    /* -- functions -- -- -- -- -- */

    /**
     * Generate an ID for an SCProductGroup model.
     *
     * @param string $productGroupName
     *
     * @return ID
     */
    static function productGroupNameToProductGroupID(
        string $productGroupName
    ): string {
        return sha1(
            "8fede984df6b327e8261bf1d7152dedf05b7bbc1 {$productGroupName}"
        );
    }


    /**
     * Generate an ID for a product group CBViewPage model.
     *
     * @param string $productGroupID
     *
     *      This function uses the product group ID as its parameter instead of
     *      the product group name so that the product group page ID can be
     *      determined even if the product group model has been deleted and we
     *      no longer have access to the product group name.
     *
     * @return string
     */
    static function productGroupIDToProductGroupPageID(
        string $productGroupID
    ): string {
        return sha1(
            "17b31e44f0bea9a47c49b0c972f51038bd43a4ad {$productGroupID}"
        );
    }


    /**
     * @param string $productGroupName
     *
     * @return string
     */
    static function
    productGroupNameToProductGroupPageURI(
        string $productGroupName
    ): string {
        $productGroupStub = CBConvert::stringToStub(
            $productGroupName
        );

        return "productgroups/{$productGroupStub}";
    }
    /* productGroupNameToProductGroupPageURI() */



    /**
     * @param string $productGroupName
     *
     * @return string
     */
    static function
    productGroupNameToProductGroupPageURL(
        string $productGroupName
    ): string {
        $productGroupURI = (
            SCProductGroup::productGroupNameToProductGroupPageURI(
                $productGroupName
            )
        );

        return "/{$productGroupURI}/";
    }
    /* productGroupNameToProductGroupPageURL() */

}

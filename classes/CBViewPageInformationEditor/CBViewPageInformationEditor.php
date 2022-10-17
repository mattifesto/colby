<?php

/**
 * @deprecated 2020_12_14
 *
 *      All of the code in this class belongs in the CBViewPageEditor class.
 *      This is not an editor of a CBViewPageInformation spec.
 */
final class
CBViewPageInformationEditor
{
    // -- CBHTMLOutput interfaces



    /**
     * @return [string]
     */
    static function
    CBHTMLOutput_JavaScriptURLs(
    ): array
    {
        $arrayOfJavaScriptURLs =
        [
            CBLibrary::buildLibraryClassFilePath(
                __CLASS__,
                '2022_10_17_1666015059',
                'js',
                cbsysurl()
            ),
        ];

        return $arrayOfJavaScriptURLs;
    }
    // CBHTMLOutput_JavaScriptURLs()



    /**
     * @return [[string, mixed]]
     */
    static function CBHTMLOutput_JavaScriptVariables() {
        return [
            [
                'CBPageClassNamesForLayouts',
                CBPagesPreferences::classNamesForLayouts()
            ],
            [
                'CBViewPageInformationEditor_administrators',
                CBViewPageInformationEditor::usersWhoAreAdministrators()
            ],
            [
                'CBViewPageInformationEditor_currentUserCBID',
                ColbyUser::getCurrentUserCBID()
            ],
            [
                'CBViewPageInformationEditor_frameClassNames',
                CBPageFrameCatalog::fetchClassNames()
            ],
            [
                'CBViewPageInformationEditor_kindClassNames',
                CBPageKindCatalog::fetchClassNames()
            ],
            [
                'CBViewPageInformationEditor_pagesAdminURL',
                CBAdmin::getAdminPageURL('CBAdminPageForPagesFind'),
            ],
            [
                'CBViewPageInformationEditor_settingsClassNames',
                CBPageSettingsCatalog::fetchClassNames()
            ],
        ];
    }
    /* CBHTMLOutput_JavaScriptVariables() */



    /**
     * @return [string]
     */
    static function CBHTMLOutput_requiredClassNames() {
        return [
            'CBAjax',
            'CBConvert',
            'CBImage',
            'CBModel',
            'CBUI',
            'CBUIActionLink',
            'CBUIBooleanEditor',
            'CBUIImageChooser',
            'CBUIPanel',
            'CBUISectionItem4',
            'CBUISelector',
            'CBUISpecPropertyEditor',
            'CBUIStringEditor2',
            'CBUIStringsPart',
            'CBUIUnixTimestampEditor',
            'Colby',
        ];
    }
    /* CBHTMLOutput_requiredClassNames */



    /* -- functions -- -- -- -- -- */



    /**
     * @return [object]
     *
     *      {
     *          name: string
     *          userCBID: CBID
     *      }
     */
    private static function usersWhoAreAdministrators(): array {
        $userGroupCBID = CBUserGroup::userGroupClassNameToCBID(
            'CBAdministratorsUserGroup'
        );

        $associations = CBModelAssociations::fetch(
            $userGroupCBID,
            'CBUserGroup_CBUser'
        );

        $userCBIDs = array_map(
            function ($association): string {
                return $association->associatedID;
            },
            $associations
        );

        $userModels = CBModels::fetchModelsByID2($userCBIDs);

        $returnValues =
        array_map(
            function (
                $userModel
            ) {
                $userFullName =
                CBUser::getName(
                    $userModel
                );

                $userModelCBID =
                CBModel::getCBID(
                    $userModel
                );

                $returnValue =
                (object)
                [
                    'name' =>
                    $userFullName,

                    'userCBID' =>
                    $userModelCBID,
                ];

                return $returnValue;
            },
            $userModels
        );

        return $returnValues;
    }
    /* usersWhoAreAdministrators() */

}

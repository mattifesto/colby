<?php

/**
 * @deprecated 2020_12_14
 *
 *      All of the code in this class belongs in the CBViewPageEditor class.
 *      This is not an editor of a CBViewPageInformation spec.
 */
final class CBViewPageInformationEditor {

    /* -- CBHTMLOutput interfaces -- -- -- -- -- */



    /**
     * @return [string]
     */
    static function
    CBHTMLOutput_JavaScriptURLs(
    ): array {
        return [
            Colby::flexpath(
                __CLASS__,
                'v675.3.js',
                cbsysurl()
            ),
        ];
    }
    /* CBHTMLOutput_JavaScriptURLs() */



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
            'CBUIStringEditor',
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

        $returnValues = array_map(
            function ($userModel) {
                return (object)[
                    'name' => $userModel->facebookName,
                    'userCBID' => $userModel->ID,
                ];
            },
            $userModels
        );

        return $returnValues;
    }
    /* usersWhoAreAdministrators() */

}

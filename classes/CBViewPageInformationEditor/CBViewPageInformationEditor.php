<?php

final class CBViewPageInformationEditor {

    /* -- CBHTMLOutput interfaces -- -- -- -- -- */



    /**
     * @return [string]
     */
    static function CBHTMLOutput_JavaScriptURLs() {
        return [
            Colby::flexpath(__CLASS__, 'v546.js', cbsysurl()),
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
                'CBViewPageInformationEditor_currentUserNumericID',
                ColbyUser::currentUserId()
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
            'CBErrorHandler',
            'CBImage',
            'CBModel',
            'CBUI',
            'CBUIActionLink',
            'CBUIBooleanEditor',
            'CBUIImageChooser',
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
     *          userNumericID: int
     *          name: string
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
                    'userNumericID' => $userModel->userNumericID,
                    'name' => $userModel->facebookName,
                ];
            },
            $userModels
        );

        return $returnValues;
    }
    /* usersWhoAreAdministrators() */

}

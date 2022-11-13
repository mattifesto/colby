<?php

final class
CBDevelopersUserGroup
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
                '2022_11_13_1668351816',
                'js',
                cbsysurl()
            ),
        ];

        return $arrayOfJavaScriptURLs;
    }
    // CBHTMLOutput_JavaScriptURLs()



    /**
     * @return [[<name>, <value>]]
     */
    static function
    CBHTMLOutput_JavaScriptVariables(
    ): array
    {
        $arrayOfJavaScriptVariables =
        [
            [
                'CBDevelopersUserGroup_currentUserIsAMember_jsvariable',
                CBUserGroup::currentUserIsMemberOfUserGroup(
                    'CBDevelopersUserGroup'
                ),
            ],
        ];

        return $arrayOfJavaScriptVariables;
    }
    // CBHTMLOutput_JavaScriptVariables()



    // -- CBInstall interfaces



    /**
     * @return void
     */
    static function
    CBInstall_install(
    ): void
    {
        $groupCBID =
        CBUserGroup::userGroupClassNameToCBID(
            __CLASS__
        );

        CBModelUpdater::update(
            (object)
            [
                'ID' =>
                $groupCBID,

                'className' =>
                'CBUserGroup',

                'userGroupClassName' =>
                __CLASS__,
            ]
        );
    }
    // CBInstall_install()



    /**
     * @return [string]
     */
    static function
    CBInstall_requiredClassNames(
    ): array
    {
        $arrayOfRequiredClassNames =
        [
            'CBUsers',
        ];

        return $arrayOfRequiredClassNames;
    }
    // CBHTMLOutput_requiredClassNames()



    // -- CBUserGroup interfaces



    /**
     * @param CBID $userCBID
     *
     * @return bool
     */
    static function
    CBUserGroup_userCanModifyMembership(
        string $userCBID
    ): bool {
        $isDeveloper = CBUserGroup::userIsMemberOfUserGroup(
            $userCBID,
            'CBDevelopersUserGroup'
        );

        return $isDeveloper;
    }
    /* CBUserGroup_userCanModifyMembership() */

}

<?php

final class
CB_CBAdmin_Code
{
    /* -- CBAdmin interfaces -- */



    /**
     * @return string
     */
    static function
    CBAdmin_getUserGroupClassName(
    ): string
    {
        return
        'CBDevelopersUserGroup';
    }
    // CBAdmin_getUserGroupClassName()



    /**
     * @return [string]
     */
    static function
    CBAdmin_menuNamePath(
    ): array
    {
        return
        [
            'develop',
            'code',
        ];
    }
    // CBAdmin_menuNamePath()



    /**
     * @return void
     */
    static function
    CBAdmin_render(
    ): void
    {
        CBHTMLOutput::pageInformation()->title =
        'Code Administration';
    }
    // CBAdmin_render()



    /* -- CBAjax interfaces -- */



    /**
     * @param object $args
     *
     * @return object
     *
     *      {
     *          command: string
     *          output: [string]
     *      }
     *
     * @return object
     *
     *      {
     *          command: string
     *          results: [string]
     *      }
     */
    static function
    CBAjax_search(
        stdClass $args
    ): object
    {
        $index =
        CBModel::valueAsInt(
            $args,
            'index'
        );

        $searchModel =
        CB_CBAdmin_Code::searches()[
            $index
        ];

        $searchCommand =
        CB_CBAdmin_Code::searchModelToSearchCommand(
            $searchModel
        );

        $searchResults =
        CB_CBAdmin_Code::searchCommandToSearchResults(
            $searchCommand
        );

        return
        (object)
        [
            'command' =>
            $searchCommand,

            'results' =>
            $searchResults,
        ];
    }
    /* CBAjax_search() */



    /**
     * @return string
     */
    static function
    CBAjax_search_getUserGroupClassName(
    ): string
    {
        return
        'CBAdministratorsUserGroup';
    }
    //CBAjax_search_getUserGroupClassName()



    /* -- CBHTMLOutput interfaces -- */



    /**
     * @return [string]
     */
    static function
    CBHTMLOutput_CSSURLs(
    ): array
    {
        return
        [
            Colby::flexpath(
                __CLASS__,
                'v675.71.css',
                cbsysurl()
            ),
        ];
    }
    // CBHTMLOutput_CSSURLs()



    /**
     * @return [string]
     */
    static function
    CBHTMLOutput_JavaScriptURLs(
    ): array
    {
        return
        [
            Colby::flexpath(
                __CLASS__,
                'v675.71.js',
                cbsysurl()
            ),
        ];
    }
    // CBHTMLOutput_JavaScriptURLs()



    /**
     * @return [[<name>, <value>]]
     */
    static function
    CBHTMLOutput_JavaScriptVariables(
    ): array
    {
        return
        [
            [
                'CB_CBAdmin_Code_searches',
                CB_CBAdmin_Code::searches(),
            ],
            [
                'CB_CBAdmin_Code_CBCodeSearch_CBID',
                cb_query_string_value(
                    'CBCodeSearch_CBID'
                ),
            ],
        ];
    }
    /* CBHTMLOutput_JavaScriptVariables() */



    /**
     * @return [string]
     */
    static function
    CBHTMLOutput_requiredClassNames(
    ): array
    {
        return
        [
            'CBErrorHandler',
            'CBMessageMarkup',
            'CBModel',
            'CBUI',
            'CBUIBooleanSwitchPart',
            'CBUIExpander',
            'Colby',
        ];
    }
    // CBHTMLOutput_requiredClassNames()



    /* -- CBInstall interfaces -- */



    /**
     * @return void
     */
    static function
    CBInstall_install(
    ): void
    {
        $developAdminMenuSpec =
        CBModels::fetchSpecByID(
            CBDevelopAdminMenu::ID()
        );

        $items =
        CBModel::valueToArray(
            $developAdminMenuSpec,
            'items'
        );

        array_push(
            $items,
            (object)
            [
                'className' =>
                'CBMenuItem',

                'name' =>
                'code',

                'text' =>
                'Code',

                'URL' =>
                CBAdmin::getAdminPageURL(
                    'CB_CBAdmin_Code'
                ),
            ]
        );

        $developAdminMenuSpec->items =
        $items;

        CBDB::transaction(
            function () use (
                $developAdminMenuSpec
            ) {
                CBModels::save(
                    $developAdminMenuSpec
                );
            }
        );
    }
    /* CBInstall_install() */



    /**
     * @return [string]
     */
    static function
    CBInstall_requiredClassNames(
    ): array
    {
        return
        [
            'CBDevelopAdminMenu',
        ];
    }
    // CBInstall_requiredClassNames()



    /* -- functions -- */



    /**
     * Implement CBCodeAdmin_searches() on each class if there are items in that
     * class that are deprecated. The method of implementing once per library is
     * not the best way.
     *
     * @NOTE 2019_07_23
     *
     *      Instructions:
     *
     *      Add each new search as a warning as long as the current code will
     *      still work. The developer of a site should fix all warnings before
     *      upgrading because at some point the code will change and the warning
     *      will become an error.
     *
     *      The searches should go in chronological order from when they are
     *      added, not in some form of alphabetical order. This indicates that
     *      found items higher up or more urgent.
     *
     *      An error means that literally the code found will no longer work and
     *      must be fixed before being put into production.
     *
     *      This is currently not perfect as some of the errors below should be
     *      warnings.
     *
     * @param object $searchModel
     *
     * @return string
     */
    static function
    searchModelToSearchCommand(
        stdClass $searchModel
    ): string
    {
        $searchCommand =
        implode(
            ' ',
            [
                'ack',
                '--heading',
                // '--underline', (enable only if ack v3 is available)
                '--match',
                escapeshellarg($searchModel->regex),
                //"--match '{$searchModel->regex}'",
                '--ignore-dir=data',

                /**
                 * @TODO 2020_04_11
                 *
                 *      Develop a more direct way to ignore the search defining
                 *      file from the files to be searched.
                 */
                '--ignore-file=match:CodeAdmin',
                '--ignore-file=match:CB_CBAdmin_Code',

                '--sort-files',
                CBModel::valueToString(
                    $searchModel,
                    'args'
                ),
            ]
        );

        $filetype =
        CBModel::valueToString(
            $searchModel,
            'filetype'
        );

        switch (
            $filetype
        ) {
            case 'js':

                $searchCommand .= ' --js';
                break;

            case 'php':

                $searchCommand .= ' --php';
                break;

            case 'args':

                break;

            default:

                $searchCommand .= ' --css --js --php';

                break;
        }

        return $searchCommand;
    }
    /* searchModelToSearchCommand() */



    /**
     * @param string $searchCommand
     *
     * @return [string]
     */
    static function
    searchCommandToSearchResults(
        string $searchCommand
    ): array
    {
        $searchResults =
        [];

        $pwd =
        getcwd();

        chdir(
            cbsitedir()
        );

        try
        {
            exec(
                "{$searchCommand} 2>&1",
                $searchResults,
                $exitCode
            );

            if (
                $exitCode === 0
            ) {

            }

            else if (
                $exitCode !== 1
            ) {
                array_push(
                    $searchResults,
                    "! returned exit code: {$exitCode}"
                );
            }
        }

        finally {
            chdir(
                $pwd
            );
        }

        return $searchResults;
    }
    /* searchCommandToSearchResults() */



    /**
     * @NOTE 2022_04_03
     *
     *      This file is getting too large. When possible implement the
     *      CBCodeAdmin_searches() interface in individual classes to specify
     *      searches.
     *
     * @return [object]
     *
     *      Returns an array of CBCodeSearch models.
     */
    static function
    searches(
    ): array
    {
        $searches =
        [];

        $allClassNames =
        CBAdmin::fetchClassNames();

        foreach (
            $allClassNames as $className
        ) {
            $function =
            "{$className}::CBCodeAdmin_searches";

            if (
                is_callable($function)
            ) {
                $interfaceReturnValue =
                call_user_func(
                    $function
                );

                $interfaceReturnValueIsAnArray =
                is_array(
                    $interfaceReturnValue
                );

                if (
                    $interfaceReturnValueIsAnArray
                ) {
                    $searches =
                    array_merge(
                        $searches,
                        $interfaceReturnValue
                    );
                }

                else
                {
                    array_push(
                        $searches,
                        $interfaceReturnValue
                    );
                }
            }
        }

        return
        array_merge(
            $searches,
            [
                /* -- errors -- -- -- -- -- */



                (object)
                [
                    'filetype' =>
                    'js',

                    'regex' =>
                    'SCShippingAddressEditorView\.storedShippingAddressModel\(',

                    'severity' =>
                    3,

                    'title' =>
                     'SCShippingAddressEditorView.storedShippingAddressModel()',

                    'cbmessage' =>
                    <<<EOT

                        This function was replaced by
                        SCShippingAddress.fetchLocalSpec().

                    EOT,

                    'errorStartDate' =>
                    '2021/02/11',
                ],



                (object)[
                    'filetype' => 'js',
                    'regex' => 'SCShippingAddressEditorView\.defaultModel\(',
                    'severity' => 3,
                    'title' => 'SCShippingAddressEditorView.defaultModel()',

                    'cbmessage' => <<<EOT

                        This function was replaced by
                        SCShippingAddress.fetchLocalSpec() and
                        SCShippingAddressEditorView.getDefaultCountryCBID().

                    EOT,

                    'errorStartDate' => '2021/02/11',
                ],



                (object)[
                    'filetype' => 'php',
                    'regex' => 'classNameForPageSettings\(',
                    'severity' => 3,
                    'title' => 'CBHTMLOutput::classNameForPageSettings()',

                    'cbmessage' => <<<EOT

                        Use CBHTMLOutput::getClassNameForPageSettings().

                    EOT,

                    'errorStartDate' => '2020/12/16',
                    'warningStartDate' => '2020/12/16',
                ],


                (object)[
                    'regex' => 'userNumericIDsToUserCBIDs',
                    'severity' => 3,
                    'title' => 'CBUsers::userNumericIDsToUserCBIDs()',

                    'cbmessage' => <<<EOT

                        Remove all uses of numeric user IDs.

                    EOT,

                    'errorStartDate' => '2020/11/25',
                    'warningStartDate' => '2020/04/15',
                ],


                (object)[
                    'regex' => 'forTesting_userCBIDtoUserNumericID',
                    'severity' => 3,
                    'title' => 'CBUsers::forTesting_userCBIDtoUserNumericID()',

                    'cbmessage' => <<<EOT

                        Remove all uses of numeric user IDs.

                    EOT,

                    'errorStartDate' => '2020/11/20',
                    'warningStartDate' => '2020/04/15',
                ],


                (object)[
                    'filetype' => 'js',
                    'regex' => 'Colby\.fetchAjaxResponse',
                    'severity' => 3,
                    'title' => 'Colby.fetchAjaxResponse()',

                    'cbmessage' => <<<EOT

                        Use CBAjax.call().

                    EOT,

                    'errorStartDate' => '2020/11/19',
                    'warningStartDate' => '2020/04/18',
                ],


                (object)[
                    'filetype' => 'js',
                    'regex' => 'CBAjax\.fetchResponse',
                    'severity' => 3,
                    'title' => 'CBAjax.fetchResponse()',

                    'cbmessage' => <<<EOT

                        Use CBAjax.call().

                    EOT,

                    'errorStartDate' => '2020/11/19',
                    'warningStartDate' => '2020/04/18',
                ],


                (object)[
                    'cbmessage' => <<<EOT

                        Use CBModels::fetchModelByCBID().

                    EOT,
                    'filetype' => 'php',
                    'regex' => 'CBImages::isInstance\(',
                    'severity' => 3,
                    'title' => 'CBImages::isInstance()',
                ],



                (object)[
                    'regex' => 'SCCartItem_getPriceInCents',
                    'severity' => 3,
                    'title' => 'SCCartItem_getPriceInCents',

                    'cbmessage' => <<<EOT

                        Implement SCCartItem_getSubtotalInCents()

                    EOT,

                    'errorStartDate' => '2020/09/08',
                    'warningStartDate' => '2020/09/08',
                ],


                (object)[
                    'filetype' => 'js',
                    'regex' => 'CBErrorHandler\.displayAndReport',
                    'severity' => 3,
                    'title' => 'CBErrorHandler.displayAndReport()',

                    'cbmessage' => <<<EOT

                        Use CBUIPanel.displayAndReportError() because
                        CBErrorHandler should not have user interface
                        functionality.

                    EOT,

                    'errorStartDate' => '2020/07/06',
                    'warningStartDate' => '2020/04/16',
                ],


                (object)[
                    'filetype' => 'php',
                    'regex' => 'COLBY_SYSTEM_DIRECTORY',
                    'severity' => 3,
                    'title' => 'Replace COLBY_SYSTEM_DIRECTORY with cbsysdir()',

                    'errorStartDate' => '2020/04/19',
                    'warningStartDate' => '2019/06/26',
                ],


                (object)[
                    'filetype' => 'php',
                    'regex' => 'CBSystemDirectory',
                    'severity' => 3,
                    'title' => 'Replace CBSystemDirectory with cbsysdir()',

                    'errorStartDate' => '2020/04/19',
                    'warningStartDate' => '2019/06/26',
                ],


                (object)[
                    'filetype' => 'js',
                    'regex' => 'Colby\.responseFromXMLHttpRequest',
                    'severity' => 3,
                    'title' => 'Colby.responseFromXMLHttpRequest()',

                    'cbmessage' => <<<EOT

                        There is no public replacement for this function.

                    EOT,

                    'errorStartDate' => '2020/04/18',
                    'warningStartDate' => '2020/04/18',
                ],


                (object)[
                    'args' => '--ignore-file=match:CBUINavigationArrowPart',
                    'regex' => 'CBUINavigationArrowPart',
                    'severity' => 3,
                    'title' => 'CBUINavigationArrowPart',

                    'cbmessage' => <<<EOT

                        Use CBUI_navigationArrow style in CBUI.css.

                    EOT,

                    'errorStartDate' => '2020/04/14',
                    'warningStartDate' => '2020/03/08',
                ],


                (object)[
                    'filetype' => 'php',
                    'regex' => 'function\s+requiredJavaScriptVariables\\(',
                    'severity' => 3,
                    'title' => 'requiredJavaScriptVariables()',

                    'cbmessage' => <<<EOT

                        Replace requiredJavaScriptVariables\(\) with
                        CBHTMLOutput_JavaScriptVariables\(\)

                    EOT,

                    'errorStartDate' => '2020/04/09',
                    'warningStartDate' => '2019/07/23',
                ],


                /**
                 * 2020_03_13 (error)
                 */
                (object)[
                    'cbmessage' => <<<EOT

                        Name model editor classes "<className>Editor".

                    EOT,
                    'regex' => 'EditorFactory',
                    'severity' => 3,
                    'title' => 'EditorFactory',
                ],


                /**
                 * 2020_02_28 (error)
                 */
                (object)[
                    'cbmessage' => <<<EOT

                        Use CBUser.signOut() from JavaScript then
                        window.location.reload() if appropriate.

                    EOT,
                    'filetype' => 'php',
                    'regex' => 'logoutURL',
                    'severity' => 3,
                    'title' => 'ColbyUser::logoutURL()',
                ],


                /**
                 * 2019_09_19 (warning)
                 * 2020_02_20 (error)
                 */
                (object)[
                    'cbmessage' => <<<EOT

                        Replace use of deprecated CBUIPanel APIs with new
                        CBUIPanel APIs.

                    EOT,
                    'filetype' => 'js',
                    'regex' => 'CBUIPanel\.(buttons|isShowing|message|reset)',
                    'severity' => 3,
                    'title' => 'Deprecated CBUIPanel APIs',
                ],


                /**
                 * 2019_11_10 (warning)
                 * 2020_02_11 (error)
                 */
                (object)[
                    'cbmessage' => <<<EOT

                        Use CBDB::SQLToObjectNullable().

                    EOT,
                    'filetype' => 'php',
                    'regex' => 'CBDB::SQLToObject\(',
                    'severity' => 3,
                    'title' => 'CBDB::SQLToObject()',
                ],


                /**
                 * 2020_02_01 (error)
                 */
                (object)[
                    'args' => '--php --js --css',
                    'cbmessage' => <<<EOT

                        The renderUserSettingsManagerViews() function no longer
                        exists on the CBUserSettingsManagerCatalog class.

                    EOT,
                    'regex' => 'renderUserSettingsManagerViews',
                    'severity' => 3,
                    'title' => (
                        'CBUserSettingsManagerCatalog::' .
                        'renderUserSettingsManagerViews'
                    ),
                ],


                /**
                 * 2020_02_01 (error)
                 */
                (object)[
                    'args' => '--php --js --css',
                    'cbmessage' => <<<EOT

                        The CBUserGroupMembershipToggleView class no longer
                        exists.

                    EOT,
                    'regex' => 'CBUserGroupMembershipToggleView',
                    'severity' => 3,
                    'title' => 'CBUserGroupMembershipToggleView',
                ],


                /**
                 * 2020_01_30 (warning)
                 * 2020_02_01 (error)
                 */
                (object)[
                    'cbmessage' => <<<EOT

                        Update all user settings managers to build their user
                        interface in JavaScript.

                    EOT,
                    'filetype' => 'php',
                    'regex' => 'CBUserSettingsManager::render',
                    'severity' => 3,
                    'title' => 'CBUserSettingsManager::render()',
                ],


                /**
                 * 2020_01_30 (warning)
                 * 2020_02_01 (error)
                 */
                (object)[
                    'cbmessage' => <<<EOT

                        Update all user settings managers to build their user
                        interface in JavaScript.

                    EOT,
                    'filetype' => 'php',
                    'regex' => 'CBUserSettingsManager_render',
                    'severity' => 3,
                    'title' => 'CBUserSettingsManager_render()',
                ],


                /**
                 * 2019_12_31 (error)
                 */
                (object)[
                    'cbmessage' => <<<EOT

                        Replace use of user numeric IDs with user CBIDs.

                    EOT,
                    'filetype' => 'php',
                    'regex' => 'updateGroupMembership',
                    'severity' => 3,
                    'title' => 'ColbyUser::updateGroupMembership()',
                ],


                /**
                 * 2019_08_10 (warning)
                 * 2019_12_31 (error)
                 */
                (object)[
                    'args' => '--ignore-file=match:ColbyUser_Tests.php',
                    'cbmessage' => <<<EOT

                        Replace use of user numeric IDs with user CBIDs.

                    EOT,
                    'filetype' => 'php',
                    'regex' => 'currentUserNumericID',
                    'severity' => 3,
                    'title' => '$currentUserNumericID',
                ],


                /**
                 * 2019_11_28 (warning)
                 * 2019_12_29 (error)
                 */
                (object)[
                    'cbmessage' => <<<EOT

                        Use CBUserGroup::userIsMemberOfUserGroup().

                    EOT,
                    'filetype' => 'php',
                    'regex' => 'isMemberOfGroup',
                    'severity' => 3,
                    'title' => 'ColbyUser::isMemberOfGroup()',
                ],


                /**
                 * 2019_12_29 (error)
                 */
                (object)[
                    'cbmessage' => <<<EOT

                        There is no replacement for this function.

                    EOT,
                    'filetype' => 'php',
                    'regex' => 'clearCachedUserGroupsForCurrentUser',
                    'severity' => 3,
                    'title' => (
                        'ColbyUser::clearCachedUserGroupsForCurrentUser()'
                    ),
                ],


                /**
                 * 2019_11_28 (warning)
                 * 2019_12_29 (error)
                 */
                (object)[
                    'cbmessage' => <<<EOT

                        Use CBUserGroup::currentUserIsMemberOfUserGroup().

                    EOT,
                    'filetype' => 'php',
                    'regex' => 'currentUserIsMemberOfGroup',
                    'severity' => 3,
                    'title' => 'ColbyUser::currentUserIsMemberOfGroup()',
                ],


                /**
                 * 2019_07_23 (warning)
                 * 2019_08_07 (error)
                 */
                (object)[
                    'filetype' => 'php',
                    'regex' => 'function\s+requiredJavaScriptURLs\\(',
                    'severity' => 3,
                    'title' => (
                        'Replace requiredJavaScriptURLs() with ' .
                        'CBHTMLOutput_JavaScriptURLs()'
                    ),
                ],


                /**
                 * 2019_08_15 (warning)
                 * 2019_10_30 (error)
                 */
                (object)[
                    'filetype' => 'php',
                    'regex' => 'CBTests_classTest',
                    'severity' => 3,
                    'title' => (
                        'Replace CBTests_classTest() with an actual test.'
                    ),
                ],


                /**
                 * 2019_08_15 (warning)
                 * 2019_11_04 (error)
                 */
                (object)[
                    'filetype' => 'php',
                    'regex' => 'resultAndExpectedToMessage',
                    'severity' => 3,
                    'title' => (
                        'Replace ' .
                        'CBConvertTests::resultAndExpectedToMessage() ' .
                        'with CBTest::resultMismatchFailure().'
                    ),
                ],


                /**
                 * 2019_10_31 (warning)
                 * 2019_11_04 (error)
                 */
                (object)[
                    'filetype' => 'php',
                    'regex' => 'CBUnitTests_tests',
                    'severity' => 3,
                    'title' => (
                        'Replace CBUnitTests_tests() with CBTest_getTests().'
                    ),
                ],


                /**
                 * 2019_11_09 (error)
                 */
                (object)[
                    'filetype' => 'php',
                    'regex' => 'isLoggedIn',
                    'severity' => 3,
                    'title' => (
                        'Replace use of ->isLoggedIn() with ' .
                        'ColbyUser::currentUserIsLoggedIn()'
                    ),
                ],


                /**
                 * 2019_11_09 (error)
                 */
                (object)[
                    'filetype' => 'php',
                    'regex' => 'isOneOfThe',
                    'severity' => 3,
                    'title' => (
                        'Replace use of ->isOneOfThe() with ' .
                        'ColbyUser::isMemberOfGroup() or ' .
                        'ColbyUser::currentUserIsMemberOfGroup()'
                    ),
                ],


                /**
                 * 2019_11_09 (error)
                 */
                (object)[
                    'filetype' => 'php',
                    'regex' => 'fetchUserDataByHash',
                    'severity' => 3,
                    'title' => (
                        'Replace use of ColbyUser::fetchUserDataByHash() ' .
                        'with CBModels::fetchModelByCBID().'
                    ),
                ],


                /**
                 * 2019_11_09 (error)
                 */
                (object)[
                    'filetype' => 'php',
                    'regex' => 'userRow',
                    'severity' => 3,
                    'title' => (
                        'Replace use of userRow() with CBUser models.'
                    ),
                ],


                /**
                 * 2019_11_09 (error)
                 */
                (object)[
                    'filetype' => 'php',
                    'regex' => 'timestampToFriendlyTime',
                    'severity' => 3,
                    'title' => (
                        'Replace use of ' .
                        'ColbyConvert::timestampToFriendlyTime() with ' .
                        'client side timestamp conversons.'
                    ),
                ],


                /**
                 * 2019_11_09 (error)
                 */
                (object)[
                    'filetype' => 'php',
                    'regex' => 'timestampToLocalUserTime',
                    'severity' => 3,
                    'title' => (
                        'Replace use of ' .
                        'ColbyConvert::timestampToLocalUserTime() with ' .
                        'client side timestamp conversons.'
                    ),
                ],


                /**
                 * 2019_11_10 (error)
                 */
                (object)[
                    'cbmessage' => <<<EOT

                        The ColbyUser class can no longer construct instances.

                    EOT,
                    'filetype' => 'php',
                    'regex' => 'ColbyUser::current\(',
                    'severity' => 3,
                    'title' => 'ColbyUser::current()',
                ],


                /**
                 * 2019_11_10 (error)
                 */
                (object)[
                    'cbmessage' => <<<EOT

                        The ColbyUser class can no longer construct instances.

                    EOT,
                    'filetype' => 'php',
                    'regex' => 'new\s+ColbyUser\(',
                    'severity' => 3,
                    'title' => 'new ColbyUser()',
                ],


                /**
                 * 2019_11_10 (error)
                 */
                (object)[
                    'cbmessage' => <<<EOT

                        Use CBUser::facebookUserIDToUserCBID().

                    EOT,
                    'filetype' => 'php',
                    'regex' => 'facebookUserIDtoUserIdentity',
                    'severity' => 3,
                    'title' => 'ColbyUser::facebookUserIDtoUserIdentity()',
                ],


                /**
                 * 2019_11_17 (error)
                 */
                (object)[
                    'cbmessage' => <<<EOT

                        Use CBUserGroup::fetchCBUserGroupModels().

                    EOT,
                    'filetype' => 'php',
                    'regex' => 'fetchGroupNames\(',
                    'severity' => 3,
                    'title' => 'ColbyUser::fetchGroupNames()',
                ],


                /**
                 * 2019_11_17 (error)
                 */
                (object)[
                    'cbmessage' => <<<EOT

                        Use CBUserGroup models.

                    EOT,
                    'filetype' => 'php',
                    'regex' => 'CBUsers::installUserGroup\(',
                    'severity' => 3,
                    'title' => 'CBUsers::installUserGroup()',
                ],


                /**
                 * 2019_11_17 (error)
                 */
                (object)[
                    'cbmessage' => <<<EOT

                        Use CBUserGroup models.

                    EOT,
                    'filetype' => 'php',
                    'regex' => 'CBUsers::uninstallUserGroup\(',
                    'severity' => 3,
                    'title' => 'CBUsers::uninstallUserGroup()',
                ],


                /**
                 * 2019_11_18 (error)
                 */
                (object)[
                    'cbmessage' => <<<EOT

                        Use CBID::generateRandomCBID().

                    EOT,
                    'filetype' => 'php',
                    'regex' => '(?<!.)random160\(',
                    'severity' => 3,
                    'title' => 'Colby::random160()',
                ],


                /**
                 * 2019_11_18 (error)
                 */
                (object)[
                    'cbmessage' => <<<EOT

                        Use CBUserGroup models.

                    EOT,
                    'filetype' => 'php',
                    'regex' => 'CBHex160',
                    'severity' => 3,
                    'title' => 'CBHex160',
                ],


                /**
                 * 2019_11_18 (error)
                 */
                (object)[
                    'cbmessage' => <<<EOT

                        Use CBModel::build().

                    EOT,
                    'filetype' => 'php',
                    'regex' => '(?<!_)toModel',
                    'severity' => 3,
                    'title' => 'CBModel::toModel()',
                ],


                /**
                 * 2019_08_10 (warning)
                 * 2019_11_23 (error)
                 */
                (object)[
                    'args' => (
                        '--php ' .
                        '--js ' .
                        '--ignore-file=match:CBUpgradesForVersion545.php ' .
                        '--ignore-file=match:CBViewPage.php ' .
                        '--ignore-file=match:CBViewPageTests.php '
                    ),
                    'cbmessage' => <<<EOT

                        The ColbyPages table "publishedBy" column and the
                        CBViewPage model "publishedBy" property are deprecated
                        and being replaced because they use numeric user IDs.

                    EOT,
                    'regex' => 'publishedBy(?!U)',
                    'severity' => 3,
                    'title' => 'publishedBy',
                ],


                /**
                 * 2019_11_21 (warning)
                 * 2019_11_24 (error)
                 *
                 * Function should be removed after all sites are updated.
                 */
                (object)[
                    'cbmessage' => <<<EOT

                        Use ColbyUser::getCurrentUserCBID().

                    EOT,
                    'filetype' => 'php',
                    'regex' => 'ColbyUser::getCurrentUserID',
                    'severity' => 3,
                    'title' => 'ColbyUser::getCurrentUserID()',
                ],


                /**
                 * 2019_07_16 (warning)
                 * 2019_11_29 (error)
                 */
                (object)[
                    'args' => '-i',
                    'cbmessage' => <<<EOT

                        Use ColbyUser::getCurrentUserCBID() and user CBIDs
                        instead of user numeric user IDs.

                    EOT,
                    'filetype' => 'php',
                    'regex' => '::currentUserId\(',
                    'severity' => 3,
                    'title' => 'ColbyUser::currentUserId()',
                ],


                /**
                 * 2019_12_02 (error)
                 */
                (object)[
                    'cbmessage' => <<<EOT

                        Use CBUserGroupMembershipToggleView.

                    EOT,
                    'filetype' => 'php',
                    'regex' => 'CBGroupUserSettings',
                    'severity' => 3,
                    'title' => 'CBGroupUserSettings',
                ],


                /**
                 * 2019_12_05 (error)
                 */
                (object)[
                    'cbmessage' => <<<EOT

                        Use CBView::renderSpec().

                    EOT,
                    'filetype' => 'php',
                    'regex' => 'renderSpecAsHTML',
                    'severity' => 3,
                    'title' => 'CBView::renderSpecAsHTML()',
                ],


                /**
                 * 2019_07_09 (warning)
                 * 2019_12_05 (error)
                 */
                (object)[
                    'cbmessage' => <<<EOT

                        Implement CBModel_build() instead of CBModel_toModel().

                    EOT,
                    'filetype' => 'php',
                    'regex' => 'CBModel_toModel',
                    'severity' => 3,
                    'title' => 'CBModel_toModel()',
                ],


                /**
                 * 2019_12_13 (error)
                 */
                (object)[
                    'cbmessage' => <<<EOT

                        Replace with CBAdmin_getUserGroupClassName().

                    EOT,
                    'filetype' => 'php',
                    'regex' => 'CBAdmin_group',
                    'severity' => 3,
                    'title' => 'CBAdmin_group()',
                ],


                /**
                 * 2019_12_04 (warning)
                 * 2019_12_14 (error)
                 */
                (object)[
                    'cbmessage' => <<<EOT

                        Use CBAjax_<name>_getUserGroupClassName().

                    EOT,
                    'filetype' => 'php',
                    'regex' => 'CBAjax_[^_]+_group',
                    'severity' => 3,
                    'title' => 'CBAjax_<name>_group',
                ],



                /* -- warnings -- -- -- -- -- */



                (object)[
                    'filetype' => 'php',
                    'regex' => 'CBPageSettings_defaultClassName',
                    'severity' => 4,
                    'title' => 'CBPageSettings_defaultClassName',

                    'cbmessage' => <<<EOT

                        This class is no longer used and should be removed.

                    EOT,

                    'warningStartDate' => '2020/12/16',
                ],


                (object)[
                    'filetype' => 'php',
                    'regex' => 'deprecatedGroupName',
                    'severity' => 4,
                    'title' => 'deprecatedGroupName',

                    'cbmessage' => <<<EOT

                        All references to the old user group names should be
                        removed.

                    EOT,

                    'warningStartDate' => '2020/12/16',
                ],


                (object)[
                    'filetype' => 'js',
                    'regex' => (
                        'CBUIPanel\.hide\('
                    ),
                    'severity' => 4,
                    'title' => 'CBUIPanel.hide()',

                    'cbmessage' => <<<EOT

                        Use CBUIPanel.hidePanelWithContentElement()

                    EOT,

                    'warningStartDate' => '2020/12/02',
                ],


                (object)[
                    'filetype' => 'js',
                    'regex' => (
                        'CBErrorHandler\.currentBrowserIsSupported(?!\()'
                    ),
                    'severity' => 4,
                    'title' => 'CBErrorHandler.currentBrowserIsSupported',

                    'cbmessage' => <<<EOT

                        Use CBErrorHandler.getCurrentBrowserIsSupported().

                    EOT,

                    'warningStartDate' => '2020/11/27',
                ],


                (object)[
                    'filetype' => 'js',
                    'regex' => 'Colby\.browserIsSupported',
                    'severity' => 4,
                    'title' => 'Colby.browserIsSupported',

                    'cbmessage' => <<<EOT

                        Use CBErrorHandler.getCurrentBrowserIsSupported().

                    EOT,

                    'warningStartDate' => '2020/11/26',
                ],


                (object)[
                    'filetype' => 'js',
                    'regex' => 'Colby\.errorToCBJavaScriptErrorModel',
                    'severity' => 4,
                    'title' => 'Colby.errorToCBJavaScriptErrorModel()',

                    'cbmessage' => <<<EOT

                        Use CBErrorHandler.errorToCBJavaScriptErrorModel().

                    EOT,

                    'warningStartDate' => '2020/11/26',
                ],


                (object)[
                    'filetype' => 'js',
                    'regex' => 'CBModel\.classFunction',
                    'severity' => 4,
                    'title' => 'CBModel.classFunction()',

                    'cbmessage' => <<<EOT

                        Use CBModel.getClassFunction().

                    EOT,

                    'warningStartDate' => '2020/09/08',
                ],


                (object)[
                    'filetype' => 'js',
                    'regex' => 'SCCartItem\.getPriceInCents',
                    'severity' => 4,
                    'title' => 'SCCartItem.getPriceInCents()',

                    'cbmessage' => <<<EOT

                        Use SCCartItem.getSubtotalInCents().

                    EOT,

                    'warningStartDate' => '2020/09/08',
                ],


                (object)[
                    'filetype' => 'php',
                    'regex' => 'SCCartItem::getPriceInCents',
                    'severity' => 4,
                    'title' => 'SCCartItem::getPriceInCents()',

                    'cbmessage' => <<<EOT

                        Use SCCartItem::getSubtotalInCents().

                    EOT,

                    'warningStartDate' => '2020/09/08',
                ],


                (object)[
                    'filetype' => 'js',
                    'regex' => 'Colby\.callAjaxFunction',
                    'severity' => 4,
                    'title' => 'Colby.callAjaxFunction()',

                    'cbmessage' => <<<EOT

                        Use CBAjax.call().

                    EOT,

                    'warningStartDate' => '2020/04/18',
                ],


                (object)[
                    'filetype' => 'js',
                    'regex' => 'Colby\.(report|reportError)',
                    'severity' => 4,
                    'title' => 'Colby.report[Error]()',

                    'cbmessage' => <<<EOT

                        Use CBErrorHandler.report().

                    EOT,

                    'warningStartDate' => '2020/02/28',
                ],


                (object)[
                    'args' => (
                        '--js ' .
                        '--ignore-file=match:CBUIStringEditor.js ' .
                        '--ignore-file=match:CBUISpecEditor.js '
                    ),
                    'cbmessage' => <<<EOT

                        Replace createEditor() with
                        CBUISpecEditor_createEditorElement2().

                    EOT,
                    'regex' => ' createEditor\W',
                    'severity' => 4,
                    'title' => 'CBUISpecEditor createEditor() interface',
                    'warningStartDate' => '2020/02/15',
                ],


                /**
                 * 2020_02_15 (warning)
                 */
                (object)[
                    'cbmessage' => <<<EOT

                        Specify email preferences in the CBEmailSender model.

                        Move the swiftmailer submodule into the root directory
                        if it is not already there.

                    EOT,
                    'filetype' => 'php',
                    'regex' => 'COLBY_EMAIL',
                    'severity' => 4,
                    'title' => 'Deprecated Email Constants',
                ],


                /**
                 * 2020_02_14 (warning)
                 */
                (object)[
                    'cbmessage' => <<<EOT

                        Specify the Facebook app ID in the CBFacebookPreferences
                        model.

                    EOT,
                    'filetype' => 'php',
                    'regex' => 'CBFacebookAppID',
                    'severity' => 4,
                    'title' => 'CBFacebookAppID',
                ],


                /**
                 * 2020_02_14 (warning)
                 */
                (object)[
                    'cbmessage' => <<<EOT

                        Specify the Facebook app secret in the
                        CBFacebookPreferences model.

                    EOT,
                    'filetype' => 'php',
                    'regex' => 'CBFacebookAppSecret',
                    'severity' => 4,
                    'title' => 'CBFacebookAppSecret',
                ],


                /**
                 * 2020_01_06 (warning)
                 */
                (object)[
                    'cbmessage' => <<<EOT

                        Use CBUIStringEditor.create() or
                        CBUIStringEditor2.create().

                    EOT,
                    'filetype' => 'js',
                    'regex' => (
                        'CBUIStringEditor\.(' .
                        'createEditor|' .
                        'createSpecPropertyEditorElement' .
                        ')'
                    ),
                    'severity' => 4,
                    'title' => 'CBUIStringEditor deprecated APIs',
                ],


                /**
                 * 2019_06_26
                 */
                (object)[
                    'args' => '--ignore-file=match:Colby.php',
                    'filetype' => 'php',
                    'regex' => 'COLBY_SITE_DIRECTORY',
                    'severity' => 4,
                    'title' => 'Replace COLBY_SITE_DIRECTORY with cbsitedir()',
                ],


                /**
                 * 2019_06_26
                 */
                (object)[
                    'args' => '--ignore-file=match:Colby.php',
                    'filetype' => 'php',
                    'regex' => 'CBSiteDirectory',
                    'severity' => 4,
                    'title' => 'Replace CBSiteDirectory with cbsitedir()',
                ],


                /**
                 * 2019_07_05
                 */
                (object)[
                    'args' => '--ignore-file=match:CBThemedMenuView',
                    'filetype' => 'php',
                    'regex' => 'CBThemedMenuView',
                    'severity' => 4,
                    'title' => 'Replace use of CBThemedMenuView',
                ],


                /**
                 * 2019_07_09
                 */
                (object)[
                    'args' => '--ignore-file=match:CBModel\.php',
                    'filetype' => 'php',
                    'regex' => 'modelToSearchText',
                    'severity' => 4,
                    'title' => (
                        'Rename modelToSearchText() to CBModel_toSearchText()'
                    ),
                ],


                /**
                 * 2019_07_09
                 */
                (object)[
                    'filetype' => 'php',
                    'regex' => 'CBModels::modelWithClassName',
                    'severity' => 4,
                    'title' => (
                        'Replace use of CBModels::modelWithClassName()'
                    ),
                ],


                /**
                 * 2019_07_23
                 */
                (object)[
                    'filetype' => 'php',
                    'regex' => 'function\s+requiredCSSURLs\\(',
                    'severity' => 4,
                    'title' => (
                        'Replace requiredCSSURLs() with ' .
                        'CBHTMLOutput_CSSURLs()'
                    ),
                ],


                /**
                 * 2019_07_23
                 */
                (object)[
                    'filetype' => 'php',
                    'regex' => 'Colby::URLForJavaScriptForSiteClass\\(',
                    'severity' => 4,
                    'title' => (
                        'Replace Colby::URLForJavaScriptForSiteClass() with ' .
                        'Colby::flexpath()'
                    ),
                ],


                /**
                 * 2019_07_26
                 */
                (object)[
                    'filetype' => 'php',
                    'regex' => 'CBPagesPreferences::ID',
                    'severity' => 4,
                    'title' => (
                        'Replace CBPagesPreferences::ID with ' .
                        'CBPagesPreferences::modelID()'
                    ),
                ],


                /**
                 * 2019_07_27
                 */
                (object)[
                    'filetype' => 'js',
                    'regex' => (
                        'CBUIImageChooser.createFullSizedChooser\\('
                    ),
                    'severity' => 4,
                    'title' => (
                        'Replace CBUIImageChooser.createFullSizedChooser() ' .
                        'with CBUIImageChooser.create().'
                    ),
                ],


                /**
                 * 2019_07_27
                 */
                (object)
                [
                    'CBCodeSearch_CBID' =>
                    'ae25c327b6cce0a7c4f720b4d13027ac5f5836ff',

                    'filetype' =>
                    'js',

                    'regex' =>
                    'CBUIImageChooser\\.createThumbnailSizedChooser\\(',

                    'severity' =>
                    4,

                    'title' =>
                    CBConvert::stringToCleanLine(<<<EOT

                        Replace CBUIImageChooser.createThumbnailSizedChooser()
                        with CBUIImageChooser.create().

                    EOT),
                ],


                /**
                 * 2019_08_18
                 */
                (object)[
                    'filetype' => 'js',
                    'regex' => 'navigateToItemCallback',
                    'severity' => 4,
                    'title' => (
                        'Remove use of "navigateToItemCallback". Any ' .
                        'navigation should be done using ' .
                        'CBUINavigationView.navigate() directly.'
                    ),
                ],


                /**
                 * 2019_08_20
                 */
                (object)[
                    'args' => '--ignore-file=match:CBUIImageChooser.js',
                    'filetype' => 'js',
                    'regex' => 'setImageURLCallback',
                    'severity' => 4,
                    'title' => (
                        'Replace use of "setImageURLCallback" with the ' .
                        '"src" property on the CBUIImageChooser object.'
                    ),
                ],


                /**
                 * 2019_08_20
                 */
                (object)[
                    'args' => '--ignore-file=match:CBUIImageChooser.js',
                    'filetype' => 'js',
                    'regex' => 'setImageURI',
                    'severity' => 4,
                    'title' => (
                        'Replace use of "setImageURI" with the ' .
                        '"src" property on the CBUIImageChooser object.'
                    ),
                ],


                /**
                 * 2019_11_18 (warning)
                 */
                (object)[
                    'cbmessage' => <<<EOT

                        Page models should have a page settings class name set
                        on a property.

                    EOT,
                    'filetype' => 'php',
                    'regex' => 'CBPageSettings::defaultClassName',
                    'severity' => 4,
                    'title' => 'CBPageSettings::defaultClassName()',
                ],


                /**
                 * 2019_11_28 (warning)
                 */
                (object)[
                    'cbmessage' => <<<EOT

                        The userNumericID property in the CBUser spec is
                        deprecated and should not be used.

                        You can use the "id" column of the ColbyUsers table
                        directly as a transitional step.

                    EOT,
                    'filetype' => 'php',
                    'regex' => 'userNumericID',
                    'severity' => 4,
                    'title' => 'userNumericID',
                ],


                /**
                 * 2019_12_14 (warning)
                 */
                (object)[
                    'cbmessage' => <<<EOT

                        Use user group class names.

                    EOT,
                    'filetype' => 'php',
                    'regex' => (
                        '[^\w\s](Public|Developers|Administrators)[^\w\s]'
                    ),
                    'severity' => 4,
                    'title' => 'deprecated user group names',
                ],



                /* -- notices -- */



                (object)[
                    'CBCodeSearch_CBID' =>
                    '47575ebc7532925602cc4cd2e0bf1b705e73bc0c',

                    'cbmessage' => <<<EOT

                        Use CBBackgroundColor1

                    EOT,

                    'regex' =>
                    '\bCBBackgroundColor\b',

                    'severity' =>
                    5,

                    'title' =>
                    'CBBackgroundColor',

                    'noticeStartDate' =>
                    '2021/09/02',

                    'noticeVersion' =>
                    675,
                ],



                (object)[
                    'CBCodeSearch_CBID' => (
                        '08fc779f0af49400a29f73b67ca33f878a348a51'
                    ),
                    'cbmessage' => <<<EOT

                        Use:

                        --- pre
                        CBModelCache::fetchModelByID(
                            CBSitePreferences::ID()
                        );
                        ---

                    EOT,
                    'filetype' => 'php',
                    'regex' => '\bCBSitePreferences\::model\(',
                    'severity' => 5,
                    'title' => 'CBSitePreferences::model()',

                    'noticeStartDate' => '2021/08/24',
                    'noticeVersion' => 675,
                ],


                (object)[
                    'CBCodeSearch_CBID' => (
                        '0bb1384bd3a5960eca4b7ab8ede0d39cd071181a'
                    ),
                    'cbmessage' => <<<EOT

                        Use CBConvert.stringToURI().

                    EOT,
                    'filetype' => 'js',
                    'regex' => '\bColby\.textToURI\(',
                    'severity' => 5,
                    'title' => 'Colby.textToURI()',

                    'noticeStartDate' => '2021/08/08',
                    'noticeVersion' => 675,
                ],


                (object)[
                    'CBCodeSearch_CBID' => (
                        '69607c2dafd879d145764ef4750b505be3cae53d'
                    ),
                    'cbmessage' => <<<EOT

                        Use CBPageSettingsForResponsivePages.

                    EOT,
                    'filetype' => 'php',
                    'regex' => '\bCBPageSettingsForAdminPages\b',
                    'severity' => 5,
                    'title' => 'CBPageSettingsForAdminPages',

                    'noticeStartDate' => '2021/06/30',
                    'noticeVersion' => 675,
                ],


                (object)[
                    'CBCodeSearch_CBID' => (
                        '883ff0dad589455caa15717f5a144e8d4fd00ea4'
                    ),
                    'cbmessage' => <<<EOT

                        Use cb_document_root_directory().

                    EOT,
                    'filetype' => 'php',
                    'regex' => '\bcbsitedir\(',
                    'severity' => 5,
                    'title' => 'cbsitedir()',

                    'noticeStartDate' => '2021/06/04',
                    'noticeVersion' => 675,
                ],


                (object)[
                    'CBCodeSearch_CBID' => (
                        '9d6720771e71d4f8c395c74e9b2b03d3232290bb'
                    ),
                    'cbmessage' => <<<EOT

                        Use CBDataStore::URLToCBID().

                    EOT,
                    'filetype' => 'php',
                    'regex' => '\bCBDataStore::URIToID\(',
                    'severity' => 5,
                    'title' => 'CBDataStore::URIToID()',

                    'noticeStartDate' => '2021/05/09',
                    'noticeVersion' => 675,
                ],


                (object)[
                    'CBCodeSearch_CBID' => (
                        '56c8fa978373245e71f9a4e95ee2339cd4317474'
                    ),
                    'cbmessage' => <<<EOT

                        Use cbsiteurl() in most cases, and possibly CBSiteURL in
                        only very specific system approved cases.

                    EOT,
                    'filetype' => 'php',
                    'regex' => '\bCOLBY_SITE_URL\b',
                    'severity' => 5,
                    'title' => 'COLBY_SITE_URL',

                    'noticeStartDate' => '2021/05/09',
                    'noticeVersion' => 675,
                ],


                (object)[
                    'CBCodeSearch_CBID' => (
                        'e8e21c025f8177c552891a19b001a28b2c2876f0'
                    ),
                    'cbmessage' => <<<EOT

                        Use cbsitedir().

                    EOT,
                    'filetype' => 'php',
                    'regex' => '\bCBSitePreferences::siteDirectory\(',
                    'severity' => 5,
                    'title' => 'CBSitePreferences::siteDirectory()',

                    'noticeStartDate' => '2021/05/09',
                    'noticeVersion' => 675,
                ],


                (object)[
                    'CBCodeSearch_CBID' => (
                        '065a8c8ae46481530591718981f5b31647feb91b'
                    ),
                    'cbmessage' => <<<EOT

                        Use CBConfiguration::primaryDomain().

                    EOT,
                    'filetype' => 'php',
                    'regex' => '\bCBSitePreferences::siteDomainName\(',
                    'severity' => 5,
                    'title' => 'CBSitePreferences::siteDomainName()',

                    'noticeStartDate' => '2021/05/09',
                    'noticeVersion' => 675,
                ],


                /**
                 * @NOTE 2021_05_08
                 *
                 *      This code search will eventually be altered to produce
                 *      errors and after that should be kept as long as the
                 *      CBSiteURL constant exists.
                 */
                (object)[
                    'args' => (
                        '--ignore-file=match:CBConfiguration.php ' .
                        '--ignore-file=match:colby-configuration.php '
                    ),
                    'CBCodeSearch_CBID' => (
                        '665424a10b299d2668723c528c2d2d9bd53dc152'
                    ),
                    'cbmessage' => <<<EOT

                        Use cbsiteurl().

                    EOT,
                    'filetype' => 'php',
                    'regex' => '\bCBSiteURL\b(?!.*\ballowed\b)',
                    'severity' => 5,
                    'title' => 'CBSiteURL',

                    'noticeStartDate' => '2021/05/08',
                    'noticeVersion' => 675,
                ],


                (object)[
                    'cbmessage' => <<<EOT

                        Use SCOrder::toSummaryCBMesssage().

                    EOT,
                    'filetype' => 'php',
                    'regex' => "SCOrder::createSummaryHTML\(",
                    'severity' => 5,
                    'title' => 'SCOrder::createSummaryHTML()',

                    'noticeStartDate' => '2021/03/24',
                    'noticeVersion' => 675,
                ],


                (object)[
                    'cbmessage' => <<<EOT

                        Colby::exceptionStackTrace() includes information that
                        can be provided by CBErrorHandler and CBRequest.

                    EOT,
                    'filetype' => 'php',
                    'regex' => "Colby::exceptionStackTrace\(",
                    'severity' => 5,
                    'title' => 'Colby::exceptionStackTrace()',

                    'noticeStartDate' => '2021/02/25',
                    'noticeVersion' => 675,
                ],


                (object)[
                    'cbmessage' => <<<EOT

                        Use CBModels::fetchModelByCBID()

                    EOT,
                    'filetype' => 'php',
                    'regex' => "CBModels::fetchModelByID\(",
                    'severity' => 5,
                    'title' => 'CBModels::fetchModelByID()',

                    'noticeStartDate' => '2021/02/15',
                    'noticeVersion' => 675,
                ],


                (object)[
                    'cbmessage' => <<<EOT

                        Use CBModels::fetchModelByCBID()

                    EOT,
                    'filetype' => 'php',
                    'regex' => "CBModels::fetchModelByIDNullable\(",
                    'severity' => 5,
                    'title' => 'CBModels::fetchModelByIDNullable()',

                    'noticeStartDate' => '2021/02/15',
                    'noticeVersion' => 675,
                ],


                (object)[
                    'cbmessage' => <<<EOT

                        Change code to use this class in an object-oriented
                        manner.

                    EOT,
                    'filetype' => 'php',
                    'regex' => "CBModelUpdater::fetch\(",
                    'severity' => 5,
                    'title' => 'CBModelUpdater::fetch()',

                    'noticeStartDate' => '2021/02/15',
                    'noticeVersion' => 675,
                ],


                (object)[
                    'cbmessage' => <<<EOT

                        Change code to use this class in an object-oriented
                        manner.

                    EOT,
                    'filetype' => 'php',
                    'regex' => "CBModelUpdater::fetchByCBID\(",
                    'severity' => 5,
                    'title' => 'CBModelUpdater::fetchByCBID()',

                    'noticeStartDate' => '2021/02/15',
                    'noticeVersion' => 675,
                ],


                (object)[
                    'cbmessage' => <<<EOT

                        Change code to use this class in an object-oriented
                        manner.

                    EOT,
                    'filetype' => 'php',
                    'regex' => "CBModelUpdater::save\(",
                    'severity' => 5,
                    'title' => 'CBModelUpdater::save()',

                    'noticeStartDate' => '2021/02/15',
                    'noticeVersion' => 675,
                ],


                (object)[
                    'cbmessage' => <<<EOT

                        Change code to use this class in an object-oriented
                        manner.

                    EOT,
                    'filetype' => 'php',
                    'regex' => "CBModelUpdater::update\(",
                    'severity' => 5,
                    'title' => 'CBModelUpdater::update()',

                    'noticeStartDate' => '2021/02/15',
                    'noticeVersion' => 675,
                ],


                (object)[
                    'cbmessage' => <<<EOT

                        Matt is figuring out the future way of doing this exact
                        operation.

                    EOT,
                    'filetype' => 'php',
                    'regex' => "CBModelUpdater::updateIfExists\(",
                    'severity' => 5,
                    'title' => 'CBModelUpdater::updateIfExists()',

                    'noticeStartDate' => '2021/02/15',
                    'noticeVersion' => 675,
                ],


                (object)[
                    'regex' => "(->ID|'ID'|\"ID\")",
                    'severity' => 5,
                    'title' => 'CBModel ID Property',
                    'cbmessage' => <<<EOT

                        Use CBModel::getCBID() or CBModel::setCBID().

                    EOT,

                    'noticeStartDate' => '2021/01/15',
                    'noticeVersion' => 675,
                ],


                (object)[
                    'regex' => 'CBPageLayout',
                    'severity' => 5,
                    'title' => 'CBPageLayout',
                    'cbmessage' => <<<EOT

                        Use CBPageFrame.

                    EOT,

                    'noticeStartDate' => '2020/12/29',
                    'noticeVersion' => 675,
                ],


                (object)[
                    'args' => (
                        '--ignore-file=match:CBUISpecEditor.js '
                    ),
                    'regex' => 'CBUISpecEditor_createEditorElement(?!2)',
                    'severity' => 5,
                    'title' => 'CBUISpecEditor_createEditorElement()',
                    'cbmessage' => <<<EOT

                        Implement CBUISpecEditor_createEditorElement2().

                    EOT,

                    'noticeStartDate' => '2020/12/28',
                    'noticeVersion' => 675,
                ],


                (object)[
                    'regex' => 'CBUI.createSectionItem\(',
                    'severity' => 5,
                    'title' => 'CBUI.createSectionItem()',
                    'cbmessage' => <<<EOT

                        In most existing uses of this function a section item is
                        no longer necessary (if there is only one element in a
                        section). Otherwise, use the CSS class CBUI_sectionItem.

                    EOT,

                    'noticeStartDate' => '2020/12/28',
                    'noticeVersion' => 675,
                ],


                (object)[
                    'regex' => 'CBUI.createSection\(',
                    'severity' => 5,
                    'title' => 'CBUI.createSection()',
                    'cbmessage' => <<<EOT

                        Use the CSS class CBUI_section.

                    EOT,

                    'noticeStartDate' => '2020/12/28',
                    'noticeVersion' => 675,
                ],


                (object)[
                    'regex' => 'CBModels::fetchSpecByIDNullable\(',
                    'severity' => 5,
                    'title' => 'CBModels::fetchSpecByIDNullable()',

                    'cbmessage' => <<<EOT

                        Use CBModels::fetchSpecByCBID()

                    EOT,

                    'noticeStartDate' => '2020/12/23',
                    'noticeVersion' => 675,
                ],


                (object)[
                    'regex' => 'CBModels::fetchSpecByID\(',
                    'severity' => 5,
                    'title' => 'CBModels::fetchSpecByID()',

                    'cbmessage' => <<<EOT

                        Use CBModels::fetchSpecByCBID()

                    EOT,

                    'noticeStartDate' => '2020/12/23',
                    'noticeVersion' => 675,
                ],


                (object)[
                    'regex' => 'CBViewPage::selectedMenuItemNames\(',
                    'severity' => 5,
                    'title' => 'CBViewPage::selectedMenuItemNames()',

                    'cbmessage' => <<<EOT

                        This function is odd and out of place, use the accessors
                        instead:

                        --- ul
                        CBViewPage::getSelectedMenuItemNames()

                        CBViewPage::getSelectedMenuItemNamesArray()

                        CBViewPage::setSelectedMenuItemNames()
                        ---

                    EOT,

                    'noticeStartDate' => '2020/12/22',
                    'noticeVersion' => 675,
                ],


                (object)[
                    'regex' => 'CBHTMLOutput::pageInformation\(',
                    'severity' => 5,
                    'title' => 'CBHTMLOutput::pageInformation()',

                    'cbmessage' => <<<EOT

                        This function allows random information to be set to the
                        CBHTMLOutput class with no control. It should be
                        replaced with individual property accessors on the
                        CBHTMLOutput class.

                    EOT,

                    'noticeStartDate' => '2020/12/22',
                    'noticeVersion' => 675,
                ],


                (object)[
                    'args' => (
                        '--ignore-file=match:CBViewPage.php ' .
                        '--ignore-file=match:CBViewPageTests.php'
                    ),
                    'regex' => '(?<!\$)selectedMainMenuItemName',
                    'severity' => 5,
                    'title' => 'selectedMainMenuItemName',

                    'cbmessage' => <<<EOT

                        This property name is deprecated on the CBViewPage
                        class. It should also be replaced with a function
                        accessor on the CBHTMLOutput class.

                    EOT,

                    'noticeStartDate' => '2020/12/22',
                    'noticeVersion' => 675,
                ],


                (object)[
                    'regex' => 'selectedMenuItemNames',
                    'severity' => 5,
                    'title' => 'selectedMenuItemNames',

                    'cbmessage' => <<<EOT

                        This property name is deprecated on the CBViewPage
                        class. It should also be replaced with a function
                        accessor on the CBHTMLOutput class.

                    EOT,

                    'noticeStartDate' => '2020/12/22',
                ],


            ]
        );
    }
    /* searches() */

}
/* CB_CBAdmin_Code */

<?php

/**
 * This class exists to facilitate the rendering of administrative pages. Its
 * render() function is called by the `handle,admin.php` handler.
 *
 * Admin pages differ from public pages in the following ways:
 *
 *      - They do not require pretty URIs
 *      - Their content is not searchable
 *      - They should not be added to the site map
 *
 * Admin pages rendered with this class will have a URI in this format:
 *
 *      /admin/c=<className>
 *
 * The class should implement the CBAdmin_render() and CBAdmin_menuNamePath()
 * interfaces. This class will add the class name to the CBHTMLOutput required
 * classes so that it can implement the CBHTMLOutput interfaces to specify its
 * own dependencies.
 *
 * CBAdmin_render() implementation is required. Even if a page is rendered
 * entirely using JavaScript, the implementation of this function should set the
 * page title.
 *
 * A single admin page class may render multiple admin pages based on another
 * query variable. They will usually be rendering similar pages. Vastly
 * different pages or pages with different sets of functionality should still
 * be separated into multiple classes.
 *
 * A class that has other HTML rendering functions, such as a view, should not
 * also render admin pages because the CBHTMLOutput dependencies will likely be
 * different.
 */
final class
CBAdmin {

    /* -- functions -- -- -- -- -- */



    /**
     * @param string $className
     *
     * @return string
     */
    static function getAdminPageURL(string $className): string {
        $classNameAsURI = $className;

        return "/admin/?c={$classNameAsURI}";
    }
    /* getAdminPageURL() */



    /**
     * @param string $className
     * @param string $pageStub (deprecated)
     *
     *      The functions should manually gather query variables if they render
     *      different content.
     *
     * @return void
     */
    static function
    render(
        string $className,
        string $pageStub
    ): void {

        /**
         * @NOTE 2019_12_13
         *
         *      Before today, the fact that handle,admin.php checked that all
         *      users were members of CBAdministratorsUserGroup was used as
         *      validation that any admin user could view any admin page without
         *      the class for that page implementing
         *      CBAdmin_getUserGroupClassName().
         *
         *      That changed today. An admin page is now required to implement
         *      CBAdmin_getUserGroupClassName() or no user will be allowed to
         *      view it.
         *
         * @TODO
         *
         *      Does handle,admin.php still need to check that users are members
         *      of the CBAdministratorsUserGroup? Probably not, then non-admin
         *      users could still use admin pages where appropriate.
         */

        $currentUserIsAuthorized = false;

        $functionName = "{$className}::CBAdmin_getUserGroupClassName";

        if (is_callable($functionName)) {
            $userGroupClassName = call_user_func($functionName);

            $currentUserIsAuthorized = CBUserGroup::userIsMemberOfUserGroup(
                ColbyUser::getCurrentUserCBID(),
                $userGroupClassName
            );
        }

        if (!$currentUserIsAuthorized) {
            include (
                cbsysdir() .
                '/handlers/handle-authorization-failed.php'
            );

            return;
        }

        CBHTMLOutput::begin();

        try {
            CBHTMLOutput::pageInformation()->classNameForPageSettings = (
                'CBPageSettingsForResponsivePages'
            );

            CBHTMLOutput::requireClassName(
                'CBAdmin'
            );

            CBHTMLOutput::requireClassName(
                'CBUI'
            );

            CBHTMLOutput::requireClassName(
                $className
            );

            $menuViewModel = (object)[
                'className' => 'CBAdminPageMenuView',
            ];

            if (is_callable($function = "{$className}::CBAdmin_initialize")) {
                call_user_func(
                    $function
                );
            }

            if (is_callable($function = "{$className}::CBAdmin_menuNamePath")) {
                $names = call_user_func(
                    $function,
                    $pageStub
                );

                CBHTMLOutput::pageInformation()->selectedMenuItemNames = (
                    $names
                );
            }

            CBView::renderSpec(
                (object)[
                    'className' => 'CB_CBView_MainHeader',
                ]
            );

            CBView::render(
                $menuViewModel
            );

            ?>

            <main class="CBUIRoot <?= $className ?>">

                <?php

                $functionName = "{$className}::CBAdmin_render";

                if (is_callable($functionName)) {
                    call_user_func($functionName, $pageStub);
                } else {
                    CBHTMLOutput::render404();
                }

                ?>

            </main>

            <?php

            CBView::render(
                (object)[
                    'className' => 'CBAdminPageFooterView',
                ]
            );

            CBHTMLOutput::render();
        } catch (Throwable $throwable) {
            CBHTMLOutput::reset();

            CBErrorHandler::report($throwable);
            CBErrorHandler::renderErrorReportPage($throwable);
        }
    }
    /* render() */



    /**
     * @deprecated 2020_08_18
     *
     *      Use CBLibrary::getAllClassDirectoryNames()
     */
    static function fetchClassNames(): array {
        return CBLibrary::getAllClassDirectoryNames();
    }
    /* fetchClassNames() */



    /**
     * Classes can implement the CBAdmin_getIssueCBMessages() interface to
     * provide system issue messages if needed. Messages should only be returned
     * if something needs to be adjusted by a developer, administrator, or user.
     * If everything is okay, no messages should be returned.
     *
     * @return [string]
     *
     *      Each string will be a CBMessage.
     */
    static function getIssueCBMessages(): array {
        $classNames = CBAdmin::fetchClassNames();
        $issueCBMessages = [];

        foreach ($classNames as $className) {
            $functionName = "{$className}::CBAdmin_getIssueCBMessages";

            if (!is_callable($functionName)) {
                /* deprecated */
                $functionName = "{$className}::CBAdmin_getIssueMessages";

                if (!is_callable($functionName)) {
                    continue;
                }
            }

            $currentIssueCBMessages = call_user_func(
                $functionName
            );

            if (!is_array($currentIssueCBMessages)) {
                throw new Exception(
                    "{$functionName}() returned a non-array value."
                );
            }

            foreach ($currentIssueCBMessages as $currentIssueCBMessage) {
                if (!is_string($currentIssueCBMessage)) {
                    throw new Exception(
                        "{$functionName}() returned an issue message " .
                        "that is not a string."
                    );
                }
            }

            $issueCBMessages = array_merge(
                $issueCBMessages,
                $currentIssueCBMessages
            );
        }
        /* foreach */

        return $issueCBMessages;
    }
    /* getIssueCBMessages() */



    /**
     * @deprecated use CBAdmin::getIssueCBMessages()
     *
     * @return [string]
     */
    static function getIssueMessages(): array {
        return CBAdmin::getIssueCBMessages();
    }

}
/* CBAdmin */

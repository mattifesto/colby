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
final class CBAdmin {

    /* -- functions -- -- -- -- -- */

    /**
     * @param string $className
     * @param string $pageStub (deprecated)
     *
     *      The functions should manually gather query variables if they render
     *      different content.
     *
     * @return void
     */
    static function render(string $className, string $pageStub): void {

        /**
         * @NOTE 2019_08_06
         *
         *      handle,admin.php ensures that all users are in the
         *      Administrators group before calling this function. The
         *      separation of that check from this function is not great.
         */

        $functionName = "{$className}::CBAdmin_group";

        if (is_callable($functionName)) {
            $group = call_user_func($functionName);

            if (!ColbyUser::currentUserIsMemberOfGroup($group)) {
                include (
                    cbsysdir() .
                    '/handlers/handle-authorization-failed.php'
                );

                return;
            }
        }

        CBHTMLOutput::begin();

        try {
            CBHTMLOutput::pageInformation()->classNameForPageSettings =
            'CBPageSettingsForAdminPages';

            CBHTMLOutput::requireClassName('CBAdmin');
            CBHTMLOutput::requireClassName('CBUI');
            CBHTMLOutput::requireClassName($className);

            $menuViewModel = (object)[
                'className' => 'CBAdminPageMenuView',
            ];

            if (is_callable($function = "{$className}::CBAdmin_initialize")) {
                call_user_func($function);
            }

            if (is_callable($function = "{$className}::CBAdmin_menuNamePath")) {
                $names = call_user_func($function, $pageStub);
                CBHTMLOutput::pageInformation()->selectedMenuItemNames = $names;
            }

            CBView::render($menuViewModel);

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
     * @return [string]
     *
     *      Returns a unique list of subdirectories of each library's classes
     *      directory.
     */
    static function fetchClassNames(): array {
        $classNames = [];

        foreach (Colby::$libraryDirectories as $libraryDirectory) {
            if ($libraryDirectory === '') {
                $libraryClassesDirectory =
                cbsitedir() .
                '/classes';
            } else {
                $libraryClassesDirectory =
                cbsitedir() .
                "/{$libraryDirectory}/classes";
            }

            $libraryClassDirectories = glob(
                "{$libraryClassesDirectory}/*",
                GLOB_ONLYDIR
            );

            $libraryClassNames = array_map(
                'basename',
                $libraryClassDirectories
            );

            $classNames = array_merge(
                $classNames,
                $libraryClassNames
            );
        }

        return array_values(
            array_unique($classNames)
        );
    }
    /* fetchClassNames() */


    /**
     * Classes can implement the CBAdmin_getIssueMessages() interface to provide
     * system issue messages if needed. Messages should only be returned if
     * something needs to be adjusted by a developer, administrator, or user. If
     * everything is okay, no messages should be returned.
     *
     * @return [string]
     */
    static function getIssueMessages(): array {
        $classNames = CBAdmin::fetchClassNames();
        $issueMessages = [];

        foreach ($classNames as $className) {
            $functionName = "{$className}::CBAdmin_getIssueMessages";

            if (is_callable($functionName)) {
                $currentIssueMessages = call_user_func($functionName);

                if (!is_array($currentIssueMessages)) {
                    throw new Exception(
                        "{$functionName}() returned a non-array value."
                    );
                }

                foreach ($currentIssueMessages as $currentIssueMessage) {
                    if (!is_string($currentIssueMessage)) {
                        throw new Exception(
                            "{$functionName}() returned an issue message " .
                            "that is not a string."
                        );
                    }
                }

                $issueMessages = array_merge(
                    $issueMessages,
                    $currentIssueMessages
                );
            }
        }
        /* foreach */

        return $issueMessages;
    }
    /* getIssueMessages() */
}
/* CBAdmin */

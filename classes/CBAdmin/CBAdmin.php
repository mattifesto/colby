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
        if (is_callable($function = "{$className}::CBAdmin_group")) {
            $group = call_user_func($function);

            if (!ColbyUser::currentUserIsMemberOfGroup($group)) {
                include cbsysdir() . '/handlers/handle-authorization-failed.php';
                return;
            }
        }

        CBHTMLOutput::begin();
        CBHTMLOutput::$classNameForSettings = 'CBPageSettingsForAdminPages';
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
            $menuViewModel->selectedMenuItemName = $names[0] ?? '';
            $menuViewModel->selectedSubmenuItemName = $names[1] ?? '';
        }

        CBView::render($menuViewModel);

        ?>

        <main class="CBUIRoot <?= $className ?>">

            <?php

            if (is_callable($function = "{$className}::CBAdmin_render")) {
                call_user_func($function, $pageStub);
            } else {
                CBHTMLOutput::render404();
            }

            ?>

        </main>

        <?php

        CBView::render((object)[
            'className' => 'CBAdminPageFooterView',
        ]);

        CBHTMLOutput::render();
    }

    /**
     * @return [string]
     *
     *      Returns a unique list of subdirectories of each library's classes
     *      directory.
     */
    static function fetchClassNames() {
        $classNames = [];

        foreach (Colby::$libraryDirectories as $libraryDirectory) {
            $libraryClassesDirectory = $libraryDirectory . '/classes';
            $libraryClassDirectories = glob("{$libraryClassesDirectory}/*" , GLOB_ONLYDIR);
            $libraryClassNames = array_map('basename', $libraryClassDirectories);

            $classNames = array_merge($classNames, $libraryClassNames);
        }

        return array_values(array_unique($classNames));
    }
}

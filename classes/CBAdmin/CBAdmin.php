<?php

final class CBAdmin {

    /**
     * @param string $className
     * @param string $pageStub
     *
     * @return void
     */
    static function render(string $className, string $pageStub): void {
        CBHTMLOutput::begin();
        CBHTMLOutput::$classNameForSettings = 'CBPageSettingsForAdminPages';
        CBHTMLOutput::requireClassName('CBUI');
        CBHTMLOutput::requireClassName($className);

        $menuViewModel = (object)[
            'className' => 'CBAdminPageMenuView',
        ];

        if (is_callable($function = "{$className}::CBAdmin_menuNamePath")) {
            $names = call_user_func($function, $pageStub);
            $menuViewModel->selectedMenuItemName = $names[0] ?? '';
            $menuViewModel->selectedSubmenuItemName = $names[1] ?? '';
        }

        CBView::render($menuViewModel);

        ?>

        <main class="CBUIRoot">

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

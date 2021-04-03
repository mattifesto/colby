<?php

final class
CBInstall {

    /**
     * In theory all databases created should have these setting by default, but
     * most likely they will not because hosted MySQL servers have different
     * defaults for various reasons.
     *
     * @NOTE 2018_08_07
     *
     *      This code, in one form or another, has existed in Colby from very
     *      early versions. However, all created tables should explicitly
     *      specify these values so its debatable whether this needs to happen.
     *      While under consideration, if a reason is discovered, add a comment.
     *
     * @return void
     */
    private static function
    alterDatabase(
    ): void {
        $SQL = <<<EOT

            ALTER DATABASE
            DEFAULT CHARSET=utf8mb4
            COLLATE=utf8mb4_unicode_520_ci

        EOT;

        Colby::query($SQL);
    }
    /* alterDatabase() */



    /**
     * @NOTE 2021_04_03
     *
     *      This function is currently called from:
     *
     *          (
     *              handle,admin.php ||
     *              CBAdminPageForUpdate::CBAjax_update()
     *          ) ->
     *          CBAdminPageForUpdate::update() ->
     *          <website|colby>/setup/update.php ->
     *          CBInstall::install()
     *
     *
     *      The "setup/update.php" file is deprecated. Implementing CBInstall
     *      interfaces is the now one and only way of performing installation
     *      tasks.
     *
     * @return void
     */
    static function
    install(
    ): void {
        CBInstall::alterDatabase();

        $allClassNames = CBAdmin::fetchClassNames();

        /**
         * A class can participate in the installation process by using one or
         * more of three techniques.
         *
         * Implement CBInstall_install()
         *
         *      This function will be called during installation.
         *
         * Implement CBInstall_requiredClassNames()
         *
         *      If class CBFoo implements CBInstall_requiredClassNames() it
         *      means two things.
         *
         *      Other classes that implement CBInstall_requiredClassNames() to
         *      return CBFoo also require the class names returned by CBFoo's
         *      implementation of CBInstall_requiredClassNames().
         *
         *      If CBFoo also implements CBInstall_install(), then CBFoo's
         *      implementation of CBInstall_install() will not be called until
         *      after all of the returned classes' implementations of
         *      CBInstall_install() have been called.
         *
         * Implement CBInstall_configure()
         *
         *      This function will be called after all of the implementations of
         *      CBInstall_install() have been called. There is no dependency
         *      sorting for calls to this interface.
         */

        $installableClassNames = array_filter(
            $allClassNames,
            function ($className) {
                return (
                    is_callable("{$className}::CBInstall_install") ||
                    is_callable("{$className}::CBInstall_requiredClassNames")
                );
            }
        );

        $installableClassNames = (
            CBRequiredClassNamesResolver::resolveRequiredClassNames(
                $installableClassNames,
                [
                    'CBInstall_requiredClassNames'
                ]
            )
        );

        /* CBInstall_install */
        foreach (
            $installableClassNames as $className
        ) {
            $functionName = "{$className}::CBInstall_install";

            if (is_callable($functionName)) {
                $functionName();
            }
        }

        /* CBInstall_configure */
        foreach (
            $allClassNames as $className
        ) {
            $functionName = "{$className}::CBInstall_configure";

            if (is_callable($functionName)) {
                $functionName();
            }
        }
    }
    /* install() */

}

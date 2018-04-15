<?php

final class CBInstall {

    /**
     * In theory all databases created should have these setting by default, but
     * most likely they will not because hosted MySQL servers have different
     * defaults for various reasons.
     */
    static function install() {
        $SQL = <<<EOT

            ALTER DATABASE
            DEFAULT CHARSET=utf8mb4
            COLLATE=utf8mb4_unicode_520_ci

EOT;

        Colby::query($SQL);

        $allClassNames = CBAdmin::fetchClassNames();

        /**
         * Installation happens by calling the CBInstall_install() interface
         * with dependencies specified by the CBInstall_requiredClassNames()
         * interface.
         */

        $installableClassNames = array_filter(
            $allClassNames,
            function ($className) {
                return is_callable("{$className}::CBInstall_install");
            }
        );

        $installableClassNames = CBRequiredClassNamesResolver::resolveRequiredClassNames(
            $installableClassNames,
            ['CBInstall_requiredClassNames']
        );

        foreach ($installableClassNames as $className) {

            /**
             * @NOTE 2018.04.15
             *
             *      It is possible for a class to have an install requirement
             *      on another class that doesn't implement the install
             *      interface. The required class name would end up in
             *      $installableClassNames. This won't hurt the installation but
             *      it might be nice to warn about this. We don't have the
             *      dependent class name here so the warning wouldn't be very
             *      helpful.
             */

            if (is_callable($function = "{$className}::CBInstall_install")) {
                $function();
            }
        }

        /**
         * Configuration happens by calling the CBInstall_configure() interface
         * after the system is fully installed. There are no dependencies
         * allowed in configuration except for the dependency that the system is
         * fully installed.
         */

        foreach ($allClassNames as $className) {
            if (is_callable($function = "{$className}::CBInstall_configure")) {
                $function();
            }
        }
    }
}

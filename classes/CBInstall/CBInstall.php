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

        /**
         * New installation process. This is placed at the end of the install
         * process at first but will eventually move earlier and become the
         * entire install process.
         */

        $allClassNames = CBAdmin::fetchClassNames();
        $installableClassNames = [];

        foreach ($allClassNames as $className) {
            CBInstall::addInstallableClassName($className, $installableClassNames);
        }

        foreach ($installableClassNames as $className) {
            call_user_func("{$className}::CBInstall_install");
            error_log("{$className}::CBInstall_install");
        }
    }

    /**
     * This function resolves and adds the installation dependencies for a class
     * name and then adds the class name to the list of installable class names
     * if the class is installable.
     */
    static function addInstallableClassName(string $className, array &$installableClassNames): void {
        if (in_array($className, $installableClassNames)) {
            return;
        }

        if (is_callable($function = "{$className}::CBInstall_requiredClassNames")) {
            $requiredClassNames = call_user_func($function);

            foreach ($requiredClassNames as $requiredClassName) {
                if (!class_exists($requiredClassName)) {
                    throw new RuntimeException("{$className} has an installation dependency on the class {$requiredClassName} which doesn't exist.");
                }

                CBInstall::addInstallableClassName($requiredClassName, $installableClassNames);
            }
        }

        if (is_callable($function = "{$className}::CBInstall_install")) {

            /**
             * The class name was not in the list of installable class names
             * when the function began. If it has been added that means some
             * class this class requires also requires this class. This is a
             * circular dependency which is impossible to resolve.
             */

            if (in_array($className, $installableClassNames)) {
                throw new RuntimeException("{$className} has a circular installation dependency.");
            }

            $installableClassNames[] = $className;
        }
    }
}

<?php

final class
CBLibrary
{
    // -- functions



    /**
     * @param string $className
     * @param string $fileVersionNumber
     * @param string $fileExtension
     * @param string $libraryPath
     *
     *      If provided, the library path should not have a trailing slash.
     *
     * @return string
     */
    static function
    buildLibraryClassFilePath(
        string $className,
        string $fileVersionNumber,
        string $fileExtension,
        string $libraryPath = ''
    ): string
    {
        $intraLibraryPath =
        "classes/{$className}/" .
        "{$className}.{$fileVersionNumber}.{$fileExtension}";

        if (
            $libraryPath === ''
        ) {
            return $intraLibraryPath;
        }

        $interLibraryPath =
        "{$libraryPath}/{$intraLibraryPath}";

        return $interLibraryPath;
    }
    // buildLibraryClassFilePath()



    /**
     * @return [string]
     *
     *      Returns a list of absolute class directories for a given class name.
     */
    static function
    getClassDirectories(
        string $className
    ): array
    {
        $classDirectories =
        [];

        foreach (
            Colby::getAbsoluteLibraryDirectories() as
            $absoluteLibraryDirectory
        ) {
            $classDirectory =
            $absoluteLibraryDirectory .
            "/classes/{$className}";

            if (
                is_dir($classDirectory)
            ) {
                array_push(
                    $classDirectories,
                    $classDirectory
                );
            }
        }

        return $classDirectories;
    }
    /* getClassDirectories() */



    /**
     * @return [string]
     *
     *      This function returns a unique list of all the directory names
     *      inside the classes directory in every library for the website. For a
     *      well maintained website this will be the list of classes for that
     *      website.
     *
     *      However, this may include class names that don't contain actual
     *      class code files, which should not happen, but this function doesn't
     *      address that issue.
     */
    static function
    getAllClassDirectoryNames(
    ): array
    {
        $classNames =
        [];

        foreach (
            Colby::getAbsoluteLibraryDirectories() as
            $absoluteLibraryDirectory
        ) {
            $libraryClassesDirectory =
            $absoluteLibraryDirectory .
            '/classes';

            $libraryClassDirectories =
            glob(
                "{$libraryClassesDirectory}/*",
                GLOB_ONLYDIR
            );

            $libraryClassNames =
            array_map(
                'basename',
                $libraryClassDirectories
            );

            $classNames =
            array_merge(
                $classNames,
                $libraryClassNames
            );
        }

        $allClassDirectoryNames =
        array_values(
            array_unique(
                $classNames
            )
        );

        return $allClassDirectoryNames;
    }
    /* getAllClassDirectoryNames() */

}

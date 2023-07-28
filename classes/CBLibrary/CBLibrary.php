<?php

final class
CBLibrary
{
    // -- functions



    /**
     * This function builds a path for a file in a library class where the
     * filename is not the same as the class name.
     *
     * @param string $className
     * @param string $filename
     * @param ?string $libraryPath
     *
     *      If provided, the library path should not have a trailing slash.
     *
     *      If this argument is an empty string the file path will be considered
     *      a root file path and be returned with a beginning slash. This will
     *      be a relative root URL.
     *
     * @return string
     */
    static function
    buildLibraryClassExtraFilePath(
        string $className,
        string $filename,
        ?string $libraryPath = null
    ): string
    {
        $intraLibraryPath =
        "classes/{$className}";

        $intraLibraryFilePath =
        "{$intraLibraryPath}/{$filename}";

        if (
            $libraryPath === null
        ) {
            $libraryClassExtraFilePath =
            $intraLibraryFilePath;
        }
        else
        {
            $libraryClassExtraFilePath =
            "{$libraryPath}/{$intraLibraryFilePath}";
        }

        return $libraryClassExtraFilePath;
    }
    // buildLibraryClassExtraFilePath()



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
        "classes/{$className}/{$className}";

        if (
            $fileVersionNumber !== ''
        ) {
            $intraLibraryPath .=
            ".{$fileVersionNumber}";
        }

        $intraLibraryPath .=
        ".{$fileExtension}";

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

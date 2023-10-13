<?php

final class
CB_Directories
{
    /**
     * This function creates a new directory that will be writeable by the
     * www-data user regardless of what user runs the function.
     *
     * @param string $absoluteDirectoryPathArgument
     *
     * @return void
     */
    static function
    createWwwDataWriteableDirectory(
        string $absoluteDirectoryPathArgument
    ): void
    {
        /**
         * Walk backward through the directories until you find the first one
         * that exists and then create all the ones that didn't.
         */
        $directoriesToCreate = [];
        $currentDirectory = $absoluteDirectoryPathArgument;

        while (
            !is_dir($currentDirectory)
        ) {
            array_unshift(
                $directoriesToCreate,
                $currentDirectory
            );

            $currentDirectory = dirname(
                $currentDirectory
            );
        }

        foreach (
            $directoriesToCreate as $directoryToCreate
        ) {
            $result =
            mkdir(
                $directoryToCreate
            );

            if (
                $result !== true
            ) {
                $message =
                CBConvert::stringToCleanLine(<<<EOT

                    Unable to create the "{$directoryToCreate}"
                    directory.

                EOT);

                throw new Exception($message);
            }

            $result =
            chgrp(
                $directoryToCreate,
                'www-data'
            );

            if (
                $result !== true
            ) {
                $message =
                CBConvert::stringToCleanLine(<<<EOT

                    Unable change the group of the
                    "{$directoryToCreate}" directory to "www-data".

                EOT);

                throw new Exception($message);
            }

            $result =
            chmod(
                $directoryToCreate,
                0775
            );

            if (
                $result !== true
            ) {
                $message =
                CBConvert::stringToCleanLine(<<<EOT

                    Unable change the permissions of the
                    "{$directoryToCreate}" directory to 0775.

                EOT);

                throw new Exception($message);
            }
        }
    }
    // createWwwDataWriteableDirectory()



    /**
     * This function returns the directory path that leads from the document
     * root directory to the colby library directory. The returned value has no
     * slash at the beginning or the end of the string.
     *
     * Typical values return by this function are:
     *
     *      colby
     *      vendor/mattifesto/colby
     */
    static function
    getRelativePathFromDocumentRootToColbyLibrary(
    ): string
    {
        static $relativePathFromDocumentRootToColbyLibrary = null;

        if (
            $relativePathFromDocumentRootToColbyLibrary === null
        ) {
            $theColbyComposerLibraryIsInstalled =
            false;

            $theInstalledVersionsClassExists =
            class_exists('\\Composer\\InstalledVersions');

            if (
                $theInstalledVersionsClassExists
            ) {
                $theColbyComposerLibraryIsInstalled =
                \Composer\InstalledVersions::isInstalled(
                    'mattifesto/colby'
                );
            }

            if (
                $theColbyComposerLibraryIsInstalled
            ) {
                $absoluteInstallPathOfColbyLibrary =
                \Composer\InstalledVersions::getInstallPath(
                    'mattifesto/colby'
                );

                $absoluteRealPathOfColbyLibrary =
                cb_realpath_without_symlink_resolution(
                    $absoluteInstallPathOfColbyLibrary
                );

                $absoluteDocumentRootDirectory =
                cb_document_root_directory();

                $parts =
                explode(
                    $absoluteDocumentRootDirectory,
                    $absoluteRealPathOfColbyLibrary
                );

                if (
                    count($parts) !== 2
                ) {
                    throw new CBException(
                        CBConvert::stringToCleanLine(<<<EOT

                            The PHP Composer of the Colby library should start
                            with the document root. It appears it doesn't for
                            some reason right now. Investigate why.

                        EOT),
                        '',
                        '071662b2e8dae4b287f19b75b7baaecaa7ec63c8'
                    );
                }

                $relativePathFromDocumentRootToColbyLibrary =
                ltrim(
                    $parts[1],
                    '/'
                );
            }
            else
            {
                /**
                 * @deprecated 2023-07-16
                 * Matt Calkins
                 *
                 * Using Colby without using it as a PHP Composer library is
                 * now officially deprecated. We hardcode the relative
                 * library directory here because it matches all current
                 * deprecated uses.
                 */

                $relativePathFromDocumentRootToColbyLibrary = 'colby';
            }

        }

        return $relativePathFromDocumentRootToColbyLibrary;
    }
    // getRelativePathFromDocumentRootToColbyLibrary()

}

<?php

final class
CBTCommand_setup {

    /* -- cbt interfaces -- */



    /**
     * @return void
     */
    static function
    cbt_execute(
    ): void {
        $configurationSpec = CB_Configuration::fetchConfigurationSpec();

        if ($configurationSpec === null) {
            echo <<<EOT
                There is no configuration file. Run 'cbt update-configuration'

            EOT;

            exit(1);
        }

        if (Colby::mysqli() === null) {
            echo "Database connection can't be created.\n";

            exit(1);
        }

        Colby::query(
            'DROP TABLE IF EXISTS cbtcommand_setup_test_table'
        );

        Colby::query(
            <<< EOT

            CREATE TABLE cbtcommand_setup_test_table
            (
                title VARCHAR(80)
            )

            EOT
        );

        Colby::query(
            'DROP TABLE cbtcommand_setup_test_table'
        );



        /* document_root/data directory */

        $dataDirectory = cb_document_root_directory() . '/data';

        if (!is_dir($dataDirectory)) {
            mkdir($dataDirectory);
        }



        /* document_root/tmp directory */

        $tmpDirectory = cb_document_root_directory() . '/tmp';

        if (!is_dir($tmpDirectory)) {
            mkdir($tmpDirectory);
        }



        /* document_root/.gitignore file */


        $gitignoreFilename = cb_document_root_directory() . '/.gitignore';
        $colbySetupDirectory = cbsysdir() . '/setup';

        if (!is_file($gitignoreFilename)) {
            copy(
                "{$colbySetupDirectory}/gitignore.template.data",
                $gitignoreFilename
            );
        }



        /* document_root/.htaccess file */

        $htaccessFilename = cb_document_root_directory() . '/.htaccess';

        if (!is_file($htaccessFilename)) {
            copy(
                "{$colbySetupDirectory}/htaccess.template.data",
                $htaccessFilename
            );
        }



        /* document_root/index.php file */

        $indexphpFilename = cb_document_root_directory() . '/index.php';

        if (!is_file($indexphpFilename)) {
            copy(
                "{$colbySetupDirectory}/index.template.data",
                $indexphpFilename
            );
        }



        /* document_root/site-configuration.php file */

        $siteconfigurationFilename = (
            cb_document_root_directory() .
            '/site-configuration.php'
        );

        if (!is_file($siteconfigurationFilename)) {
            copy(
                "{$colbySetupDirectory}/site-configuration.template.data",
                $siteconfigurationFilename
            );
        }



        /* empty favicon files */

        touch(
            cb_document_root_directory() .
            '/favicon.gif'
        );

        touch(
            cb_document_root_directory() .
            '/favicon.ico'
        );



        /* version.php */

        $websiteVersionFilepath = (
            cb_document_root_directory() .
            '/version.php'
        );

        if (!is_file($websiteVersionFilepath)) {
            copy(
                "{$colbySetupDirectory}/version.template.data",
                $websiteVersionFilepath
            );
        }



        /* document_root/classes */

        $documentRootClassesDirectory = (
            cb_document_root_directory() .
            '/classes'
        );

        if (!is_dir($documentRootClassesDirectory)) {
            mkdir($documentRootClassesDirectory);

            /**
             * We only create the template files when we create the classes
             * directory because we don't want to create more of them or
             * overwrite the current ones.
             */

            $namespace = CBTCommand_setup::inputNamespace();

            $templates = [
                ['BlogPostPageKind', 'php'],
                ['BlogPostPageTemplate', 'php'],
                ['Menu_main', 'php'],
                ['PageFooterView', 'php'],
                ['PageFooterView', 'css'],
                ['PageFrame', 'php'],
                ['PageHeaderView', 'php'],
                ['PageSettings', 'php'],
                ['PageTemplate', 'php'],
                ['Page_blog', 'php'],
            ];

            $colbyTemplateDirectory = "{$colbySetupDirectory}/templates";

            foreach ($templates as $template) {
                $templateName = $template[0];
                $extension = $template[1];

                $sourceFilepath =
                "{$colbyTemplateDirectory}/{$templateName}.{$extension}";

                $destinationDirectory =
                "{$documentRootClassesDirectory}/{$namespace}{$templateName}";

                $destinationFilepath =
                "{$destinationDirectory}/{$namespace}{$templateName}.{$extension}";

                if (!is_dir($destinationDirectory)) {
                    mkdir($destinationDirectory);
                }

                $content = file_get_contents($sourceFilepath);
                $content = preg_replace('/PREFIX/', $namespace, $content);

                $content = preg_replace_callback(
                    '/RANDOMID/',
                    function () {
                        $ID = CBID::generateRandomCBID();
                        return "'{$ID}'";
                    },
                    $content
                );

                file_put_contents(
                    $destinationFilepath,
                    $content
                );
            }
        }

        CBInstall::install();



        /* create user account */

        $existingUserCBID = CBUser::emailToUserCBID(
            $primaryAdministratorEmailAddress
        );

        if ($existingUserCBID === null) {
            $password1 = null;

            while (true) {
                echo "enter password: ";

                $password1 = CBTCommand_setup::inputHiddenText();

                echo "\n";

                $passwordIssues = CBUser::passwordIssues($password1);

                if ($passwordIssues !== null) {
                    echo $passwordIssues, "\n";

                    continue;
                }

                echo "confirm password: ";

                $password2 = CBTCommand_setup::inputHiddenText();

                echo "\n";

                if ($password2 !== $password1) {
                    echo "password doesn't match\n";

                    continue;
                }

                break;
            }

            $userCBID = CBID::generateRandomCBID();


            $userSpec = CBModel::createSpec(
                'CBUser',
                $userCBID
            );

            $passwordHash = password_hash(
                $password1,
                PASSWORD_DEFAULT
            );

            if ($passwordHash === false) {
                throw new CBException(
                    'An error occured while hashing your password.'
                );
            }

            CBUser::setPasswordHash(
                $userSpec,
                $passwordHash
            );

            CBUser::setEmailAddress(
                $userSpec,
                $primaryAdministratorEmailAddress
            );

            CBDB::transaction(
                function () use ($userSpec) {
                    CBModels::save(
                        $userSpec
                    );
                }
            );

            CBUserGroup::addUsers(
                'CBAdministratorsUserGroup',
                $userCBID
            );

            CBUserGroup::addUsers(
                'CBDevelopersUserGroup',
                $userCBID
            );
        }
    }
    /* cbt_execute() */



    /* -- functions -- */



    /**
     * @return string
     */
    static function
    inputHiddenText(
    ): string {
        system(
            'stty -echo'
        );

        try {
            $value = (
                fgets(STDIN)
            );
        } finally {
            system(
                'stty echo'
            );
        }

        return $value;
    }
    /* inputHiddenText() */



    /**
     * @return string
     *
     *      This function will not return until it has a valid namespace.
     */
    static function
    inputNamespace(
    ): string {
        while (true) {
            echo 'enter namespace ([A-Z][A-Z0-9]*): ';

            $value = (
                trim(
                    fgets(STDIN),
                )
            );

            echo "\n";

            if (
                CBTCommand_setup::isNamespace($value)
            ) {
                break;
            }
        }

        return $value;
    }
    /* inputNamespace() */



    /**
     * @param string $potentialNamespace
     *
     * @return bool
     */
    static function
    isNamespace(
        string $potentialNamespace
    ): bool {
        $result = preg_match(
            '/^[A-Z][A-Z0-9]*$/',
            $potentialNamespace
        );

        return ($result === 1);
    }
    /* isNamespace() */

}

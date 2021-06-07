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

        $p = (object)[];

        $p->primaryWebsiteDomain = CB_Configuration::getPrimaryWebsiteDomain(
            $configurationSpec
        );

        /* TODO validate */



        $p->databaseHost = CB_Configuration::getDatabaseHost(
            $configurationSpec
        );

        /* TODO validate */



        $p->databaseUsername = CB_Configuration::getDatabaseUsername(
            $configurationSpec
        );

        /* TODO validate */



        $p->databasePassword = CB_Configuration::getDatabasePassword(
            $configurationSpec
        );

        /* TODO validate */



        $primaryAdministratorEmailAddress = CB_Configuration::getPrimaryAdministratorEmailAddress(
            $configurationSpec
        );

        /* TODO validate */



        $p->primaryAdministratorPassword = '';

        /* TODO ask for password */



        $p->primaryAdministratorPassword2 = '';

        /* TODO ask for password */




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

        touch(cb_document_root_directory() . '/favicon.gif');
        touch(cb_document_root_directory() . '/favicon.ico');



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
        }

        /* TODO ask for namespace */
        $namespace = 'CBX';

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
                trim(
                    fgets(STDIN),
                )
            );
        } finally {
            system(
                'stty echo'
            );
        }

        return $value;
    }
    /* inputHiddenText() */

}

<?php

/**
 * This class is used to help render the /admin/ page.
 */
final class
CBStatusAdminPage {

    /* -- CBAdmin interfaces -- -- -- -- -- */



    /**
     * @return string
     */
    static function CBAdmin_getUserGroupClassName(): string {
        return 'CBAdministratorsUserGroup';
    }



    /**
     * @param string $pageStub
     *
     * @return [string]
     */
    static function CBAdmin_menuNamePath(string $pageStub): array {
        return [
            'general',
            'status'
        ];
    }
    /* CBAdmin_menuNamePath() */



    /**
     * @return void
     */
    static function
    CBAdmin_render(
    ): void {
        CBHTMLOutput::pageInformation()->title = 'Website Status';

        $siteNameAsMessage = CBMessageMarkup::stringToMessage(
            CBSitePreferences::siteName()
        );

        $environment = CBSitePreferences::getEnvironment(
            CBSitePreferences::model()
        );

        switch (
            $environment
        ) {
            case 'CBSitePreferences_environment_development':
                $environmentAsMessage = 'Development';
                break;

            case 'CBSitePreferences_environment_testing':
                $environmentAsMessage = 'Testing';
                break;

            case 'CBSitePreferences_environment_staging':
                $environmentAsMessage = 'Staging';
                break;

            case 'CBSitePreferences_environment_production':
                $environmentAsMessage = 'Production';
                break;

            default:
                $environmentAsMessage = 'Unknown';
        }

        $message = <<<EOT

            --- CBUI_sectionContainer
                --- CBUI_section
                    --- CBUI_container_leftAndRight
                        --- p CBUI_textColor2
                        Site Name
                        ---
                        {$siteNameAsMessage}
                    ---
                    --- CBUI_container_leftAndRight
                        --- p CBUI_textColor2
                        Site Environment
                        ---
                        {$environmentAsMessage}
                    ---
                ---
            ---

            --- CBUI_title1
            Versions
            ---
        EOT;

        echo CBMessageMarkup::messageToHTML(
            $message
        );


        /* status widgets */

        $statusWidgetFilepaths = Colby::globFiles(
            'classes/CBStatusWidgetFor*'
        );

        $statusWidgetClassNames = array_map(
            function (
                $filepath
            ) {
                return basename(
                    $filepath,
                    '.php'
                );
            },
            $statusWidgetFilepaths
        );

        sort(
            $statusWidgetClassNames
        );

        $statusWidgetMessage = '';

        foreach (
            $statusWidgetClassNames as $statusWidgetClassName
        ) {
            $functionName = "{$statusWidgetClassName}::CBStatusAdminPage_data";

            if (
                is_callable($functionName)
            ) {
                $data = call_user_func(
                    $functionName
                );

                $keyAsMessage = CBMessageMarkup::stringToMessage(
                    $data[0]
                );

                $valueAsMessage = CBMessageMarkup::stringToMessage(
                    $data[2]
                );

                $statusWidgetMessage .= <<<EOT

                    --- CBUI_container_leftAndRight
                        --- p CBUI_textColor2
                        {$keyAsMessage}
                        ---

                        {$valueAsMessage}
                    ---

                EOT;
            }
        }

        if (
            $statusWidgetMessage !== ''
        ) {
            $statusWidgetMessage = <<<EOT

                --- CBUI_sectionContainer
                    --- CBUI_section
                        {$statusWidgetMessage}
                    ---
                ---

            EOT;

            echo CBMessageMarkup::messageToHTML(
                $statusWidgetMessage
            );
        }
    }
    /* CBAdmin_render() */



    /* -- CBAjax interfaces -- -- -- -- -- */



    /**
     * @return object
     *
     *      {
     *          issueCBMessages: [string]
     *          duplicateURICBMessages: [string]
     *      }
     */
    static function CBAjax_fetchMessages(): stdClass {
        return (object)[
            'issueCBMessages' => (
                CBStatusAdminPage::getIssueCBMessages()
            ),
            'duplicateURICBMessages' => (
                CBStatusAdminPage::getDuplicateURICBMessages()
            ),
        ];
    }
    /* CBAjax_fetchMessages() */



    /**
     * @return string
     */
    static function CBAjax_fetchMessages_getUserGroupClassName(): string {
        return 'CBAdministratorsUserGroup';
    }



    /* -- CBHTMLOutput interfaces -- */



    /**
     * @return [string]
     */
    static function
    CBHTMLOutput_JavaScriptURLs(
    ): array {
        return [
            Colby::flexpath(
                __CLASS__,
                'v675.56.js',
                cbsysurl()
            ),
        ];
    }
    /* CBHTMLOutput_JavaScriptURLs() */



    /**
     * @return [string]
     */
    static function
    CBHTMLOutput_requiredClassNames(
    ): array {
        return [
            'CBAjax',
            'CBModel',
            'CBUI',
            'CBUIMessagePart',
            'CBUIPanel',
            'Colby',
        ];
    }
    /* CBHTMLOutput_requiredClassNames() */



    /* -- functions -- -- -- -- -- */



    /**
     * @return string
     */
    static function duplicateURIToMessage($URI): string {
        $message = '';
        $URIAsSQL = CBDB::stringToSQL($URI);

        $SQL = <<<EOT

            SELECT      LOWER(HEX(CBModels.ID)) AS ID,
                        title

            FROM        CBModels

            JOIN        ColbyPages
                ON      CBModels.ID = ColbyPages.archiveID

            WHERE       URI = {$URIAsSQL} AND
                        published IS NOT NULL

            ORDER BY    published ASC

        EOT;

        $data = CBDB::SQLToObjects($SQL);

        foreach ($data as $datum) {
            $editURL = cbsiteurl() . "/admin/?c=CBModelEditor&ID={$datum->ID}";
            $message .= "\"{$datum->title}\" (edit (a {$editURL}))\n\n";
        }

        $message = <<<EOT

            /{$URI}/

            {$message}

        EOT;

        return $message;
    }
    /* duplicateURIToMessage() */



    /**
     * @return [string]
     */
    private static function getDuplicateURICBMessages(): array {
        $messages = [];

        $SQL = <<<EOT

            SELECT      `URI`
            FROM        `ColbyPages`
            WHERE       `published` IS NOT NULL AND
                        `URI` IS NOT NULL
            GROUP BY    `URI`
            HAVING      COUNT(*) > 1

        EOT;

        $URIs = CBDB::SQLToArray($SQL);

        if (!empty($URIs)) {
            foreach ($URIs as $URI) {
                $messages[] = CBStatusAdminPage::duplicateURIToMessage($URI);
            }
        }

        return $messages;
    }
    /* getDuplicateURICBMessages() */



    /**
     * @return [string]
     */
    private static function getIssueCBMessages() {
        $currentUserIsDeveloper = CBUserGroup::currentUserIsMemberOfUserGroup(
            'CBDevelopersUserGroup'
        );

        if ($currentUserIsDeveloper !== true) {
            return [];
        }

        $issueCBMessages = [];

        if (
            sha1_file(cbsitedir() . '/.gitignore') !==
            sha1_file(cbsysdir() . '/setup/gitignore.template.data')
        ) {
            $message = <<<EOT

                The website (.gitigore (code)) file is different than the
                (colby/setup/gitigore.template.data (code)) file.

            EOT;

            array_push($issueCBMessages, $message);
        }

        if (
            sha1_file(cbsitedir() . '/.htaccess') !==
            sha1_file(cbsysdir() . '/setup/htaccess.template.data')
        ) {
            $message = <<<EOT

                The website (.htaccess (code)) file is different than the
                (colby/setup/htaccess.template.data (code)) file.

            EOT;

            array_push($issueCBMessages, $message);
        }

        $issueCBMessages = array_merge(
            $issueCBMessages,
            CBAdmin::getIssueCBMessages()
        );

        return $issueCBMessages;
    }
    /* getIssueCBMessages() */

}
/* CBStatusAdminPage */

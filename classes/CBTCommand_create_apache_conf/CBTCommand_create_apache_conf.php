<?php

final class
CBTCommand_create_apache_conf {

    /* -- cbt interfaces -- */



    /**
     * @return void
     */
    static function
    cbt_execute(
    ): void {
        $configurationSpec = CB_Configuration::fetchConfigurationSpec();

        $serverSpecificWebsiteDomain = (
            CB_Configuration::getServerSpecificWebsiteDomain(
                $configurationSpec
            )
        );

        $projectDirectory = cb_project_directory();

        $serverAdminEmailAddress = (
            CB_Configuration::getPrimaryAdministratorEmailAddress(
                $configurationSpec
            )
        );

        $documentRootDirectory = cbsitedir();

        $logsDirectory = cb_logs_directory();

        if ($logsDirectory === null) {
            echo "This command only works with colby projects.\n";
            return;
        }

        $websiteDomains = [
            $serverSpecificWebsiteDomain,
        ];

        $primaryWebsiteDomain = CB_Configuration::getPrimaryWebsiteDomain(
            $configurationSpec
        );

        if ($primaryWebsiteDomain !== $serverSpecificWebsiteDomain) {
            array_push(
                $websiteDomains,
                $primaryWebsiteDomain
            );
        }

        $websiteDomains = array_merge(
            $websiteDomains,
            CB_Configuration::getSecondaryWebsiteDomains(
                $configurationSpec,
            )
        );

        foreach ($websiteDomains as $websiteDomain) {
            $vh1 = <<<EOT
            <VirtualHost *:80>
                ServerName      {$websiteDomain}
                ServerAdmin     {$serverAdminEmailAddress}

                DocumentRoot    {$documentRootDirectory}
                ErrorLog        {$logsDirectory}/error.log
                CustomLog       {$logsDirectory}/access.log combined

                <Directory "{$documentRootDirectory}">
                    AllowOverride   all
                    Require         all granted
                </Directory>
            </VirtualHost>

            EOT;

            $reverseWebsiteDomain = CBConvert::domainToReverseDomain(
                $websiteDomain
            );

            $confFilename = (
                "{$projectDirectory}/{$reverseWebsiteDomain}.conf"
            );

            file_put_contents(
                $confFilename,
                $vh1
            );

            $localServerDomain = CBConvert::domainToLocalServerDomain(
                $websiteDomain
            );

            if ($localServerDomain === null) {
                continue;
            }



            /* user home directory */

            $userInformation = posix_getpwuid(
                posix_getuid()
            );

            $userHomeDirectory = $userInformation[
                'dir'
            ];



            $sslCertificateFile = (
                "{$userHomeDirectory}/.acme.sh/" .
                "{$localServerDomain}/fullchain.cer"
            );

            $sslCertificateKeyFile = (
                "{$userHomeDirectory}/.acme.sh/" .
                "{$localServerDomain}/{$localServerDomain}.key"
            );

            $vh2 = <<<EOT
            <VirtualHost *:443>
                ServerName      {$websiteDomain}
                ServerAdmin     {$serverAdminEmailAddress}

                DocumentRoot    {$documentRootDirectory}
                ErrorLog        {$logsDirectory}/error.log
                CustomLog       {$logsDirectory}/access.log combined

                SSLEngine               on
                SSLCertificateFile      {$sslCertificateFile}
                SSLCertificateKeyFile   {$sslCertificateKeyFile}

                <Directory "{$documentRootDirectory}">
                    AllowOverride   all
                    Require         all granted
                </Directory>
            </VirtualHost>

            EOT;

            $confFilename = (
                "{$projectDirectory}/{$reverseWebsiteDomain}_ssl.conf"
            );

            file_put_contents(
                $confFilename,
                $vh2
            );
        }
    }
    /* cbt_execute() */

}

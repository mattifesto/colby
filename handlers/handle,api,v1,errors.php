<?php

/**
 * @NOTE 20230702
 * Matt Calkins
 *
 *      This API returns a list of errors with error files in the error
 *      directory. It's very early and uncertain if this API will last.
 */

require_once(
    cb_document_root_directory() .
    '/vendor/autoload.php'
);

$configurationSpec =
CB_Configuration::fetchConfigurationSpec();

if (
    $configurationSpec === null
) {
    $databaseHost =
    '';
}
else
{
    $databaseHost =
    CB_Configuration::getDatabaseHost(
        $configurationSpec
    );
}

if (
    $databaseHost !==
    ''
) {

    throw new CBException(
        'This API is currently only available when no database is available.',
        '',
        '612a88df540bc6e140450e0f7c62f64892df7407'
    );
}

$files =
glob(
    cb_document_root_directory() .
    '/errors/*.txt'
);


echo
json_encode(
    $files,
    JSON_PRETTY_PRINT
),
"\n";

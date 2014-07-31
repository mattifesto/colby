<?php

/**
 * This handler is always included from another handler. If this handler
 * decides to output a page it will call `exit` to prevent the caller from
 * generating a second page after this handler returns.
 *
 * This handler will always output an entire page or nothing at all.
 */

$databaseIsAvailable = COLBY_MYSQL_HOST && COLBY_MYSQL_DATABASE && COLBY_MYSQL_USER;

if ($databaseIsAvailable)
{
    $sql = <<<EOT

        SELECT
            COUNT(*) AS `count`
        FROM
            `information_schema`.`ROUTINES`
        WHERE
            `ROUTINE_SCHEMA` = DATABASE() AND
            `ROUTINE_TYPE` = 'FUNCTION' AND
            `ROUTINE_NAME` = 'ColbySchemaVersionNumber'

EOT;

    $result                 = Colby::query($sql);
    $databaseIsInstalled    = $result->fetch_object()->count;

    $result->free();

    if ($databaseIsInstalled)
    {
        return;
    }
}

CBHTMLOutput::begin();
CBHTMLOutput::setTitleHTML('Installation');
CBHTMLOutput::setDescriptionHTML('This page performs the initial installation of a Colby website.');

include CBSystemDirectory . '/sections/admin-page-settings.php';

?>

<main>
    <style scoped>

        main
        {
            text-align: center;
        }

        main > header
        {
            margin: 100px 0 50px;
        }

        main progress
        {
            width:  100px;
            margin: 50px;
        }

    </style>

    <?php

    if ($databaseIsAvailable)
    {
        ?>

        <header><h1>Database Installation Required</h1></header>
        <div>
            <progress id="database-intallation-progress" value="0"></progress>
            <div>
                <button onclick="DatabaseInstaller.installDatabase(this);">Install Database</button>
            </div>
        </div>

        <?php
    }
    else
    {
        ?>

        <header><h1>Database Configuration Required</h1></header>
        <div>
            <div style="width: 400px; margin: 50px auto; line-height: 1.5;">
                <p>The database configuration section in
                <p style="margin: 20px 0;"><code>colby-configuration.php</code>
                <p>has not been completed. Please complete this section of the
                   configuration file before proceeding.
            </div>
            <div>
                <button onclick="location.reload();">Check Configuration</button>
            </div>
        </div>

        <?php
    }

    ?>

</main>

<script>

"use strict";

var DatabaseInstaller = {};

DatabaseInstaller.installDatabase = function(sender)
{
    sender.disabled = true;

    var xhr = new XMLHttpRequest();

    var handleAjaxResponse = function()
    {
        var response = Colby.responseFromXMLHttpRequest(xhr);

        if (response.wasSuccessful)
        {
            location.reload();
        }
        else
        {
            Colby.displayResponse(response);
        }

        document.getElementById('database-intallation-progress').setAttribute('value', 0);

        sender.disabled = false;
    };

    var formData = new FormData();
    formData.append('requestIsForInitialInstallation', true);

    xhr.open('POST', '/developer/update/ajax/perform-update/', true);
    xhr.onload = handleAjaxResponse;
    xhr.send(formData);

    document.getElementById('database-intallation-progress').removeAttribute('value');
};

</script>

<?php

include CBSystemDirectory . '/sections/admin-page-footer-2.php';

CBHTMLOutput::render();

exit;

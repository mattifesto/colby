<?php

/**
 * This handler is always included from another handler. If this handler
 * decides to output a page it should call `exit` to prevent the caller from
 * generating a second page after this handler returns.
 *
 * This handler will always output an entire page or nothing at all.
 */

$page = ColbyOutputManager::beginPage('Installation',
                                      'This website needs to be installed.',
                                      'simple');

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

$result = Colby::query($sql);

$databaseIsInstalled = $result->fetch_object()->count;

$result->free();

if ($databaseIsInstalled)
{
    $page->discard();

    return;
}

?>

<section class="widget"
         style="text-align: center;">
    <header><h1>Database Installation Required</h1></header>
    <div>
        <progress id="database-intallation-progress"
                  value="0"
                  style="width: 50px; margin: 50px;"></progress>
        <div>
            <button onclick="DatabaseInstaller.installDatabase(this);">Install Database</button>
        </div>
    </div>
</section>

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

    xhr.open('POST', '/developer/configuration/ajax/setup-database/', true);
    xhr.onload = handleAjaxResponse;
    xhr.send();

    document.getElementById('database-intallation-progress').removeAttribute('value');
}

</script>

<?php

$page->end();

exit;

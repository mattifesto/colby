<section class="widget">
    <header><h1>Database Status</h1></header>
    <div style="text-align: center; position: relative;">
        <progress id="database-upgrade-progress"
                  value="0"
                  style="position: absolute; width: 50px; top: 5px; right: 5px;"></progress>

        <?php

        $sql = 'SELECT ColbySchemaVersionNumber() AS `schemaVersionNumber`';

        $result = Colby::query($sql);

        $schemaVersionNumber = $result->fetch_object()->schemaVersionNumber;

        $result->free();

        ?>

        <div style="color: gray; font-size: 12px; font-weight: bold; text-transform: uppercase;">Schema version</div>
        <div id="schema-version-number"
             style="margin-top: -10px; font-size: 40px; font-weight: bold;"><?php echo $schemaVersionNumber; ?></div>

        <div>
            <button onclick="DatabaseWidget.upgradeDatabase(this);">Upgrade Database</button>
        </div>
    </div>
</section>

<script>

"use strict";

var DatabaseWidget = {};

DatabaseWidget.upgradeDatabase = function(sender)
{
    sender.disabled = true;

    var xhr = new XMLHttpRequest();

    var handleAjaxResponse = function()
    {
        var response = Colby.responseFromXMLHttpRequest(xhr);

        if (response.wasSuccessful)
        {
            document.getElementById('schema-version-number').textContent = response.schemaVersionNumber;
        }
        else
        {
            Colby.displayResponse(response);
        }

        document.getElementById('database-upgrade-progress').setAttribute('value', 0);

        sender.disabled = false;
    };

    xhr.open('POST', '/developer/configuration/ajax/setup-database/', true);
    xhr.onload = handleAjaxResponse;
    xhr.send();

    document.getElementById('database-upgrade-progress').removeAttribute('value');
}

</script>

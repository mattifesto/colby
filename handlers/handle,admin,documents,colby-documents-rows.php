<?php

include_once COLBY_SYSTEM_DIRECTORY . '/snippets/shared/documents-administration.php';

$page = new ColbyOutputManager('admin-html-page');

$page->titleHTML = 'ColbyDocuments Table Rows';
$page->descriptionHTML = 'Documents that are in the ColbyDocuments table.';

$page->begin();

if (!ColbyUser::current()->isOneOfThe('Developers'))
{
    include Colby::findSnippet('authenticate.php');

    goto done;
}

$sql = <<<EOT
SELECT
    LOWER(HEX(`groupId`)) as `groupId`,
    LOWER(HEX(`modelId`)) as `typeId`,
    LOWER(HEX(`archiveId`)) as `archiveId`
FROM
    `ColbyPages`
ORDER BY
    `groupId`, `modelId`
EOT;

$result = Colby::query($sql);

/**
 * Aggregate data to simplify report generation.
 */

while ($row = $result->fetch_object())
{
    if (!isset($sections) ||
        $row->groupId != $section->groupId ||
        $row->typeId != $section->typeId)
    {
        if (!isset($sections))
        {
            $sections = array();
        }

        $section = new stdClass();

        $section->groupId = $row->groupId;
        $section->typeId = $row->typeId;
        $section->archiveIds = new ArrayObject();

        $sections[] = $section;
    }

    $section->archiveIds->append($row->archiveId);
}

$result->free();

?>

<nav style="text-align: center; margin-bottom: 20px;">
    <?php renderDocumentsAdministrationMenu(); ?>
</nav>

<main>
    <style scoped>

        section.group-type a
        {
            margin: 5px 15px;
        }

        section.group-type .hash
        {
            font-size: 0.5em;
        }

        section.group-type header
        {
            margin-bottom: 10px;
        }

        section.group-type h1
        {
            font-size: 1em;
        }

        section.group-type + section.group-type
        {
            margin-top: 30px;
        }

    </style>

    <h1>ColbyDocuments Table Rows</h1>

    <?php

    foreach ($sections as $section)
    {
        ?>

        <section class="group-type">
            <header>
                <h1>Group Id: <?php echo $section->groupId; ?></h1>
                <h1>Type Id: <?php echo $section->typeId; ?></h1>
            </header>

            <?php

            foreach ($section->archiveIds as $archiveId)
            {
                echo linkForArchiveId($archiveId), "\n";
            }

            ?>

        </section>

        <?php
    }

    ?>

</main>

<?php

done:

$page->end();


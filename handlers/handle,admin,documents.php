<?php

include_once COLBY_DIRECTORY . '/classes/ColbyDocument.php';
include_once COLBY_DIRECTORY . '/snippets/shared/documents-administration.php';

$page = new ColbyOutputManager('admin-html-page');

$page->titleHTML = 'Archives';
$page->descriptionHTML = 'List, view, delete, and manage archives.';

$page->begin();

if (!ColbyUser::current()->isOneOfThe('Developers'))
{
    include Colby::findSnippet('authenticate.php');

    goto done;
}

$document = ColbyDocument::documentWithArchiveId(COLBY_DOCUMENTS_ADMINISTRATION_SHARED_ARCHIVE_ID);

$archive = $document->archive();

?>

<main>
    <h1>Archives</h1>

    <section>
        <style scoped>
            dl + dl
            {
                margin-top: 20px;
            }

            dd
            {
                margin: 10px 10px 0px;
            }
        </style>

        <h1>Stray Archives</h1>

        <?php

        $strayArchives = $archive->valueForKey('strayArchives');
        $strayArchives->uasort('compareDocumentGroupIds');

        if ($strayArchives)
        {

            echo '<div>';

            $isFirstIteration = true;

            foreach ($strayArchives as $archiveId => $archiveData)
            {
                if ($isFirstIteration ||
                    $documentGroupId != $archiveData->documentGroupId)
                {
                    $isFirstIteration = false;

                    $documentGroupId = $archiveData->documentGroupId;

                    echo "<h2>Group: {$documentGroupId}</h2>";
                }

                echo viewLinkForArchiveId($archiveId), ' ';
            }

            echo '</div>';
        }

        ?>

        <h1>Stray Documents</h1>

        <?php

        $strayDocuments = $archive->valueForKey('strayDocuments');
        $strayDocuments->uasort('compareDocumentGroupIds');

        if ($strayDocuments)
        {

            echo '<div>';

            $isFirstIteration = true;

            foreach ($strayDocuments as $archiveId => $archiveData)
            {
                if ($isFirstIteration ||
                    $documentGroupId != $archiveData->documentGroupId)
                {
                    $isFirstIteration = false;

                    $documentGroupId = $archiveData->documentGroupId;

                    echo "<h2>Group: {$documentGroupId}</h2>";
                }

                echo viewLinkForArchiveId($archiveId), ' ';
            }

            echo '</div>';
        }

        ?>

    </section>

    <div style="text-align: center;">
        <progress value="0" max="256" id="progress" style="margin-bottom: 20px;"></progress><br>
        <a class="big-button" onclick="ColbyArchivesExplorer.regenerateDocument();">Regenerate Archives Document</a>
    </div>
</main>

<script src="<?php echo COLBY_URL . '/handlers/handle,admin,documents.js'; ?>"></script>

<?php

done:

$page->end();

/* ---------------------------------------------------------------- */

/**
 *
 */
function viewLinkForArchiveId($archiveId, $key = null)
{
    if ($key)
    {
        echo ', ';
    }

    echo "<a href=\"/admin/documents/view/?archive-id={$archiveId}\">{$archiveId}</a>";
}

/**
 *
 */
function compareDocumentGroupIds($left, $right)
{
    if ($left->documentGroupId == $right->documentGroupId)
    {
        return 0;
    }

    if ($left->documentGroupId > $right->documentGroupId)
    {
        return 1;
    }
    else
    {
        return -1;
    }
}

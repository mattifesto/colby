<?php

define('COLBY_ARCHIVES_DOCUMENT_ARCHIVE_ID', '5bda1825fe0be9524106061b910fd0b8e1dde0c2');

include_once COLBY_DIRECTORY . '/classes/ColbyDocument.php';

$page = new ColbyOutputManager('admin-html-page');

$page->titleHTML = 'Archives';
$page->descriptionHTML = 'List, view, delete, and manage archives.';

$page->begin();

if (!ColbyUser::current()->isOneOfThe('Developers'))
{
    include Colby::findSnippet('authenticate.php');

    goto done;
}

$document = ColbyDocument::documentWithArchiveId(COLBY_ARCHIVES_DOCUMENT_ARCHIVE_ID);

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

        $parts = $archive->valueForKey('parts');

        for ($i = 0; $i < 256; $i++)
        {
            $hexIndex = sprintf('%02x', $i);

            if (isset($parts->{$hexIndex}))
            {
                $partObject = $parts->{$hexIndex};

                ?>

                <dl>
                    <dt>part: <?php echo $hexIndex; ?></dt>

                    <?php

                    if (isset($partObject->strayArchiveIds) &&
                        !empty($partObject->strayArchiveIds))
                    {
                        $countOfStrayArchiveIds = count($partObject->strayArchiveIds);

                        ?>

                        <dd>Stray archive ids: <?php array_walk($partObject->strayArchiveIds, 'viewLinkForArchiveId'); ?></dd>

                        <?php
                    }

                    if (isset($partObject->strayDocumentArchiveIds) &&
                        !empty($partObject->strayDocumentArchiveIds))
                    {
                        $countOfStrayDocumentArchiveIds = count($partObject->strayDocumentArchiveIds);

                        ?>

                        <dd>Stray document archive ids: <?php array_walk($partObject->strayDocumentArchiveIds, 'viewLinkForArchiveId'); ?></dd>

                        <?php
                    }

                    ?>

                </dl>

            <?php

            }
        }

        ?>

    </section>

    <div style="text-align: center;">
        <progress value="0" max="256" id="progress" style="margin-bottom: 20px;"></progress><br>
        <a class="big-button" onclick="ColbyArchivesExplorer.regenerateDocument();">Regenerate Archives Document</a>
    </div>
</main>

<script src="<?php echo COLBY_URL . '/handlers/handle,developer,archives.js'; ?>"></script>

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

    echo "<a href=\"/developer/archives/view/?archive-id={$archiveId}\">{$archiveId}</a>";
}

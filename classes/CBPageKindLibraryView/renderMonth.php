<section class="CBPageKindLibraryView month">

    <header class="CBTextBoxView <?= $headerThemeClass ?>">
        <h1><?= $titleAsHTML ?></h1>
        <div>
            <nav>
                <a href="<?= $URLAsHTML, ColbyConvert::textToHTML(CBRequest::canonicalQueryString()); ?>">Most Recent</a> |
                <a href="<?= $URLAsHTML, ColbyConvert::textToHTML(CBRequest::canonicalQueryString([['CBPageKindLibraryViewType','library']])); ?>">Library</a>
            </nav>
        </div>
    </header>

    <?php

    if ($summaries === 'invalid month') {
        echo '<p>invalid month';
    } else if (empty($summaries)) {
        echo '<p>There were no pages published during this month.';
    } else {
        array_walk($summaries, function($summaryModel) use ($summaryThemeClass) {
            CBPageKindLibraryView::renderPageSummaryModelAsHTML($summaryModel, $summaryThemeClass);
        });
    }

    ?>

</section>

<section class="CBPageKindView month">

    <header class="CBTextBoxView <?= $headerThemeClass ?>">
        <h1><?= $titleAsHTML ?></h1>
        <div>
            <nav>
                <a href="<?= $URLAsHTML, ColbyConvert::textToHTML(CBRequest::canonicalQueryString()); ?>">Most Recent</a> |
                <a href="<?= $URLAsHTML, ColbyConvert::textToHTML(CBRequest::canonicalQueryString([['CBPageKindViewType','library']])); ?>">Library</a>
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
            CBPageKindView::renderPageSummaryModelAsHTML($summaryModel, $summaryThemeClass);
        });
    }

    ?>

</section>

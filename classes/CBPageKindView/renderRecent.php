<section class="CBPageKindView recent">

    <header class="CBTextBoxView <?= $headerThemeClass ?>">
        <h1>Most Recent</h1>
        <div>
            <nav>
                <a href="<?= $URLAsHTML, ColbyConvert::textToHTML(CBRequest::canonicalQueryString([['CBPageKindViewType','library']])); ?>">Library</a>
            </nav>
        </div>
    </header>

    <?php

    array_walk($summaries, function($summaryModel) use ($summaryThemeClass) {
        CBPageKindView::renderPageSummaryModelAsHTML($summaryModel, $summaryThemeClass);
    });

    ?>

</section>

<section class="CBPageKindLibraryView recent">

    <header class="CBTextBoxView <?= $headerThemeClass ?>">
        <h1>Most Recent</h1>
        <div>
            <nav>
                <a href="<?= $URLAsHTML, ColbyConvert::textToHTML(CBRequest::canonicalQueryString([['CBPageKindLibraryViewType','library']])); ?>">Library</a>
            </nav>
        </div>
    </header>

    <?php

    array_walk($summaries, function($summaryModel) use ($summaryThemeClass) {
        CBPageKindLibraryView::renderPageSummaryModelAsHTML($summaryModel, $summaryThemeClass);
    });

    ?>

</section>

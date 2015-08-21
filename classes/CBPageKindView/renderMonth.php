<section class="CBPageKindView month <?= $themeClass ?>">

    <header>
        <h1><?= $titleAsHTML ?></h1>
        <nav>
            <a href="<?= ColbyConvert::textToHTML(CBRequest::canonicalQueryString()); ?>">Most Recent</a> |
            <a href="<?= ColbyConvert::textToHTML(CBRequest::canonicalQueryString([['CBPageKindViewType','library']])); ?>">Library</a>
        </nav>
    </header>

    <?php

    if ($summaries === 'invalid month') {
        echo '<p>invalid month';
    } else if (empty($summaries)) {
        echo '<p>There were no pages published during this month.';
    } else {
        array_walk($summaries, 'CBPageKindView::renderPageSummaryModelAsHTML');
    }

    ?>

</section>

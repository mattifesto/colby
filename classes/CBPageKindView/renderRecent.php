<section class="CBPageKindView recent <?= $themeClass ?>">

    <header>
        <h1>Most Recent</h1>
        <nav>
            <a href="<?= ColbyConvert::textToHTML(CBRequest::canonicalQueryString([['CBPageKindViewType','library']])); ?>">Library</a>
        </nav>
    </header>

    <?php array_walk($summaries, 'CBPageKindView::renderPageSummaryModelAsHTML'); ?>

</section>

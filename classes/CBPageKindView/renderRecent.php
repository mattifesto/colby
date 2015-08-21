<section class="CBPageKindView recent <?= $themeClass ?>">

    <header>
        <h1>Most Recent</h1>
        <nav>
            <a href="<?= ColbyConvert::textToHTML(CBRequest::canonicalQueryString([['CBPageKindViewType','library']])); ?>">Library</a>
        </nav>
    </header>

    <?php array_walk($summaries, function($summary) { ?>

        <article>
                <div class="thumbnail">
                    <img src="<?= $summary->thumbnailURL ?>" alt="">
                </div>
                <div class="content">
                    <h1><a href="<?= CBSiteURL . "/{$summary->URI}" ?>">
                        <?= $summary->titleHTML ?>
                    </a></h1>

                    <p><?= $summary->descriptionHTML ?>
                </div>
        </article>

    <?php }); ?>

</section>

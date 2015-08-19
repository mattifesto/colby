<section class="CBPageKindView recent <?= $themeClass ?>">

    <h1>Most Recent</h1>

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

    <nav>
        <a href="<?= ColbyConvert::textToHTML(CBRequest::canonicalQueryString([['CBPageKindViewType','catalog']])); ?>">Catalog</a>
    </nav>
</section>

<section class="CBPageKindView month <?= $themeClass ?>">
    <header>
        <h1><?= $_GET['CBPageKindViewMonth'] ?></h1>
        <nav>
            <a href="<?= ColbyConvert::textToHTML(CBRequest::canonicalQueryString()); ?>">Most Recent</a> |
            <a href="<?= ColbyConvert::textToHTML(CBRequest::canonicalQueryString([['CBPageKindViewType','library']])); ?>">Library</a>
        </nav>
    </header>
    <p><?= $_GET['CBPageKindViewMonth'] ?>
</section>

<?php

$page = ColbyOutputManager::beginVerifiedUserPage('Markaround Help',
                                                  'Help for markaround syntax.',
                                                  'admin');

$markaround = file_get_contents(COLBY_SITE_DIRECTORY . '/colby/snippets/markaround-help.txt');

?>

<article>
    <header><h1>Markaround Help</h1></header>

    <div style="font-size: 14px;">
        <div class="formatted-content">
            <?php echo ColbyConvert::markaroundToHTML($markaround); ?>
        </div>
    </div>
</article>

<?php

$page->end();

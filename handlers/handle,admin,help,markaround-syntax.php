<?php

$page = ColbyOutputManager::beginVerifiedUserPage('Markaround Help',
                                                  'Help for markaround syntax.',
                                                  'admin');

$markaroundText = file_get_contents(COLBY_SITE_DIRECTORY . '/colby/snippets/markaround-help.txt');

?>

<h1>Markaround Help</h1>

<div style="width: 700px; margin: 0px auto; font-size: 14px;">
    <div class="formatted-content">
        <?php echo ColbyConvert::markaroundTextToHTML($markaroundText); ?>
    </div>
</div>

<?php

$page->end();

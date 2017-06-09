<?php

if (!ColbyUser::current()->isOneOfThe('Administrators')) {
    return include CBSystemDirectory . '/handlers/handle-authorization-failed.php';
}

CBHTMLOutput::$classNameForSettings = 'CBPageSettingsForAdminPages';
CBHTMLOutput::setTitleHTML('Markaround Help');
CBHTMLOutput::setDescriptionHTML('Help for markaround syntax.');
CBHTMLOutput::begin();
CBHTMLOutput::addCSSURL(CBSystemURL . '/css/standard-formatted-content.css');

CBView::renderModelAsHTML((object)[
    'className' => 'CBAdminPageMenuView',
    'selectedMenuItemName' => 'help',
    'selectedSubmenuItemName' => 'markaround-syntax',
]);

$markaround = file_get_contents(CBSystemDirectory . '/snippets/help/markaround.txt');

?>

<main style="margin: 0 auto; max-width: 640px; padding: 40px 10px;">
    <div class="formatted-content standard-formatted-content">
        <?php echo CBMarkaround::markaroundToHTML($markaround); ?>
    </div>
</main>

<?php

CBAdminPageFooterView::renderModelAsHTML();

CBHTMLOutput::render();

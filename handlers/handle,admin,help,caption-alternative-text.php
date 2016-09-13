<?php

if (!ColbyUser::current()->isOneOfThe('Administrators')) {
    return include CBSystemDirectory . '/handlers/handle-authorization-failed.php';
}

CBHTMLOutput::$classNameForSettings = 'CBPageSettingsForAdminPages';
CBHTMLOutput::begin();
CBHTMLOutput::setTitleHTML('Captions and Alternative Text Help');
CBHTMLOutput::setDescriptionHTML('Help for creating effective captions and alternative text.');
CBHTMLOutput::addCSSURL(CBSystemURL . '/css/standard-formatted-content.css');

CBAdminPageMenuView::renderModelAsHTML((object)[
    'selectedMenuItemName' => 'help',
    'selectedSubmenuItemName' => 'caption-alternative-text',
]);

$markaround = file_get_contents(CBSystemDirectory . '/snippets/help/caption-alternative-text.txt');

?>

<main style="margin: 0 auto; max-width: 640px; padding: 40px 10px;">
    <div class="formatted-content standard-formatted-content">
        <?php echo CBMarkaround::markaroundToHTML($markaround); ?>
    </div>
</main>

<?php

CBAdminPageFooterView::renderModelAsHTML();

CBHTMLOutput::render();

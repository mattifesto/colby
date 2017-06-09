<?php

if (!ColbyUser::current()->isOneOfThe('Administrators')) {
    return include CBSystemDirectory . '/handlers/handle-authorization-failed.php';
}

CBHTMLOutput::$classNameForSettings = 'CBPageSettingsForAdminPages';
CBHTMLOutput::begin();
CBHTMLOutput::setTitleHTML('Titles and Descriptions Help');
CBHTMLOutput::setDescriptionHTML('Help for creating effective titles and descriptions.');
CBHTMLOutput::addCSSURL(CBSystemURL . '/css/standard-formatted-content.css');

CBView::renderModelAsHTML((object)[
    'className' => 'CBAdminPageMenuView',
    'selectedMenuItemName' => 'help',
    'selectedSubmenuItemName' => 'title-subtitle',
]);

$markaround = file_get_contents(COLBY_SITE_DIRECTORY . '/colby/snippets/help/title-subtitle.txt');

?>

<main style="margin: 0 auto; max-width: 640px; padding: 40px 10px;">
    <div class="formatted-content standard-formatted-content">
        <?php echo CBMarkaround::markaroundToHTML($markaround); ?>
    </div>
</main>

<?php

CBAdminPageFooterView::renderModelAsHTML();

CBHTMLOutput::render();

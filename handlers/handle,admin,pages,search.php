<?php

if (!ColbyUser::current()->isOneOfThe('Administrators')) {
    return include CBSystemDirectory . '/handlers/handle-authorization-failed.php';
}


CBHTMLOutput::begin();
CBHTMLOutput::$classNameForSettings = 'CBPageSettingsForAdminPages';
CBHTMLOutput::setTitleHTML('Search for Pages');
CBHTMLOutput::setDescriptionHTML('Search for pages to edit.');

CBHTMLOutput::addCSSURL(CBSystemURL . '/handlers/handle,admin,pages.css');
CBHTMLOutput::addCSSURL(CBSystemURL . '/handlers/handle,admin,pages,search.css');
CBHTMLOutput::addJavaScriptURL(CBSystemURL . '/handlers/handle,admin,pages.js');
CBHTMLOutput::addJavaScriptURL(CBSystemURL . '/handlers/handle,admin,pages,search.js');

$selectedMenuItemID     = 'pages';
$selectedSubmenuItemID  = 'search';

include CBSystemDirectory . '/sections/admin-page-menu.php';

?>

<main class="CBSystemFont">

    <div id="CBPagesSearchFormView"></div>

    <table class="list-of-pages standard-admin-spacing standard-cell-ellipses">
        <thead><tr>
            <th class="actions-cell" style="width: 150px;"></th>
            <th class="title-cell">Title</th>
            <th class="publication-date-cell">Publication Date</th>
        </tr></thead>
        <tbody id="queryResults" class="standard-admin-row-colors">
        </tbody>
    </table>

</main>

<?php

include CBSystemDirectory . '/sections/admin-page-footer-2.php';

CBHTMLOutput::render();

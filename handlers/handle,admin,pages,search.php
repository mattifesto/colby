<?php

include_once CBSystemDirectory . '/classes/CBHTMLOutput.php';


if (!ColbyUser::current()->isOneOfThe('Administrators'))
{
    return include CBSystemDirectory . '/handlers/handle-authorization-failed.php';
}


CBHTMLOutput::begin();
CBHTMLOutput::setTitleHTML('Search for Pages');
CBHTMLOutput::setDescriptionHTML('Search for pages to edit.');

include CBSystemDirectory . '/sections/equalize.php';

CBHTMLOutput::addCSSURL(CBSystemURL . '/css/admin.css');
CBHTMLOutput::addCSSURL(CBSystemURL . '/handlers/handle,admin,pages.css');
CBHTMLOutput::addCSSURL('https://fonts.googleapis.com/css?family=Source+Sans+Pro:400');
CBHTMLOutput::addCSSURL('https://fonts.googleapis.com/css?family=Source+Sans+Pro:700');
CBHTMLOutput::addJavaScriptURL(CBSystemURL . '/javascript/Colby.js');
CBHTMLOutput::addJavaScriptURL(CBSystemURL . '/handlers/handle,admin,pages.js');


$selectedMenuItemID     = 'pages';
$selectedSubmenuItemID  = 'search';

include CBSystemDirectory . '/sections/admin-page-menu.php';

?>

<main>

    <div style="text-align: center;">
        <input type="text" id="queryText" class="big-field" style="width: 600px;">
    </div>
    <div style="text-align: center; margin: 30px 0px;">
        <button onclick="CBPagesAdmin.searchForPages();">search</button>
    </div>

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

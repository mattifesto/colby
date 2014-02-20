<?php

include_once CBSystemDirectory . '/classes/CBHTMLOutput.php';


CBHTMLOutput::setTitleHTML('Search for Pages');
CBHTMLOutput::setDescriptionHTML('Search for pages to edit.');
CBHTMLOutput::addCSSURL(CBSystemURL . '/handlers/handle,admin,pages.css');
CBHTMLOutput::addJavaScriptURL(CBSystemURL . '/handlers/handle,admin,pages.js');
CBHTMLOutput::begin();


if (!ColbyUser::current()->isOneOfThe('Administrators'))
{
    include Colby::findSnippet('authenticate.php');

    // TODO: this should display a whole new page instead of a snippet and return instead of goto

    goto done;
}


include CBSystemDirectory . '/sections/admin-page-header.php';

$selectedMenuItemID     = 'pages';
$selectedSubmenuItemID  = 'search';

include CBSystemDirectory . '/sections/admin-page-menu.php';

?>

<main>

    <div style="text-align: center;">
        <input type="text" id="queryText" class="big-field" style="width: 600px;">
    </div>
    <div style="text-align: center; margin: 30px 0px;">
        <a class="big-button" onclick="CBPagesAdmin.searchForPages();">search</a>
    </div>

    <table class="list-of-pages standard-admin-spacing standard-cell-ellipses">
        <thead><tr>
            <th class="actions-cell"></th>
            <th class="title-cell">Title</th>
            <th class="publication-date-cell">Publication Date</th>
        </tr></thead>
        <tbody id="queryResults" class="standard-admin-row-colors">
        </tbody>
    </table>

</main>

<?php

include CBSystemDirectory . '/sections/admin-page-footer.php';

done:

CBHTMLOutput::render();

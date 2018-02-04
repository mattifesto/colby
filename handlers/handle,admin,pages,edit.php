<?php

if (!ColbyUser::current()->isOneOfThe('Administrators')) {
    return include CBSystemDirectory . '/handlers/handle-authorization-failed.php';
}

if (!isset($_GET['data-store-id'])) {
    if (isset($_GET['id-to-copy'])) {
        $IDToCopy = $_GET['id-to-copy'];
        $IDToCopy = "&id-to-copy={$IDToCopy}";
    } else {
        $IDToCopy = '';
    }

    $ID = CBHex160::random();

    header("Location: /admin/pages/edit/?data-store-id={$ID}{$IDToCopy}");

    exit;
}


CBHTMLOutput::begin();
CBHTMLOutput::$classNameForSettings = 'CBPageSettingsForAdminPages';
CBHTMLOutput::pageInformation()->title = 'Page Editor';
CBHTMLOutput::requireClassName('CBDefaultEditor');
CBHTMLOutput::requireClassName('CBViewPageEditor');

/**
 * Export page templates
 */

$templateclassNames = CBPageTemplates::templateClassNames();

foreach ($templateclassNames as $templateClassName) {
    $specAsJSON = json_encode(call_user_func("{$templateClassName}::CBModelTemplate_spec"));

    if (is_callable($function = "{$templateClassName}::CBModelTemplate_title")) {
        $title = call_user_func($function);
    } else {
        $title = 'Unnamed Template';
    }

    $descriptor = (object)[
        'specAsJSON' => $specAsJSON,
        'title' => $title,
    ];

    CBHTMLOutput::exportListItem('CBPageTemplateDescriptors', $templateClassName, $descriptor);
}

CBView::render((object)[
    'className' => 'CBAdminPageMenuView',
    'selectedMenuItemName' => 'pages',
]);

?>

<main class="CBUIRoot">
</main>

<?php

CBView::render((object)[
    'className' => 'CBAdminPageFooterView',
]);

CBHTMLOutput::render();

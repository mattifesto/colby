<?php

$pageSpec = CBModelTemplateCatalog::fetchLivePageTemplate();

CBModel::merge($pageSpec, (object)[
    'title' => 'Shopping Cart',
    'description' => 'View the items you have placed in your shopping cart.',
    'sections' => [
        (object)[
            'className' => 'SCShoppingCartView',
        ],
    ],
]);

CBPage::renderSpec($pageSpec);

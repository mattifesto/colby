<?php

CBPage::renderSpec(
    CBModelTemplateCatalog::fetchLivePageTemplate(
        (object)[
            'title' => 'Shipping Address',
            'description' => 'Enter the address to which you would like your order shipped.',
            'sections' => [
                (object)[
                    'className' => 'SCShippingAddressEditorView',
                ],
            ],
        ]
    )
);

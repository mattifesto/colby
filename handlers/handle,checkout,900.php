<?php

CBPage::renderSpec(
    CBModelTemplateCatalog::fetchLivePageTemplate(
        (object)[
            'title' => 'Checkout',
            'description' => 'Complete your purchase.',
            'sections' => [
                (object)[
                    'className' => 'SCCheckoutView',
                ],
            ],
        ]
    )
);

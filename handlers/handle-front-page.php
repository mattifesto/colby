<?php

$message = <<<EOT

        --- center
        This is the default front page rendered by:

        (colby/handlers/handle-front-page.php (code))

        To replace this, create a page in the admin area and set it as the front
        page. A front page created in the admin area has higher priority than a
        handler.

        Alternatively, you can create this file to render your front page
        content.
        
        (handlers/handle-front-page.php (code))

        ---

EOT;

$spec = CBModelTemplateCatalog::fetchLivePageTemplate();

CBModel::merge($spec, (object)[
    'title' => 'Default Front Page',
    'sections' => [
        (object)[
            'className' => 'CBMessageView',
            'markup' => $message,
        ],
    ],
]);

CBPage::renderSpec($spec);

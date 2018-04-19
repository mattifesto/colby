<?php

$message = <<<EOT

        --- center
        This is the default front page. To replace it create the file:

        (handlers/handle-front-page.php (code))

        in the main website directory. Alternatively, create a page in the admin
        area and set it as the front page. A front page created in the admin
        area has higher priority than a handler.
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

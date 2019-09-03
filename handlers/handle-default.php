<?php

header($_SERVER['SERVER_PROTOCOL'] . ' 404 Not Found');

$URL = cbsiteurl() . htmlspecialchars(
    preg_replace(
        '/([\/\?&])/',
        ' $1 ',
        $_SERVER['REQUEST_URI']
    )
);

$URLAsMessage = CBMessageMarkup::stringToMessage($URL);

$message = <<<EOT

    --- center
    The page you requested was not found.

    ({$URLAsMessage} (code))
    ---

EOT;

CBPage::renderSpec(
    CBModelTemplateCatalog::fetchLivePageTemplate(
        (object)[
            'title' => 'Page Not Found',
            'sections' => [
                (object)[
                    'className' => 'CBMessageView',
                    'markup' => $message,
                ]
            ]
        ]
    )
);

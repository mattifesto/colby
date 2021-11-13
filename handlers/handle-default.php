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

$cbmessage = <<<EOT

    --- center
    The page you requested was not found.

    ({$URLAsMessage} (code))
    ---

EOT;

$pageSpec = CBViewPage::standardPageTemplate();

CBViewPage::setTitle(
    $pageSpec,
    'Page Not Found'
);

CBViewPage::setViews(
    $pageSpec,
    [
        (object)[
            'className' => 'CBMessageView',
            'markup' => $cbmessage,
        ]
    ]
);

CBPage::renderSpec(
    $pageSpec
);

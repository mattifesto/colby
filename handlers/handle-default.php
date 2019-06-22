<?php

header($_SERVER['SERVER_PROTOCOL'] . ' 404 Not Found');

$URL = cbsiteurl() . htmlspecialchars(
    preg_replace(
        '/([\/\?&])/',
        ' $1 ',
        $_SERVER['REQUEST_URI']
    )
);

$cm = <<<EOT
The page you requested was not found.

`{$URL}`
EOT;

CBPage::renderSpec(
    (object)[
        'className' => 'CBViewPage',
        'title' => 'Page Not Found',
        'layout' => (object)[
            'className' => 'CBPageLayout',
        ],
        'sections' => [
            (object)[
                'className' => 'CBTextView2',
                'contentAsCommonMark' => $cm,
                'CSSClassNames' => 'center',
            ],
        ],
    ]
);

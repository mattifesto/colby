<?php

$content = <<<EOT

This is the default front page. To replace it create the file

`handlers/handle-front-page.php`

in the main website directory. Alternatively, create a page in the admin area
and set it as the front page. A front page created in the admin area has higher
priority than a handler.

EOT;

CBPage::renderSpec((object)[
    'className' => 'CBViewPage',
    'title' => 'Default Front Page',
    'layout' => (object)[
        'className' => 'CBPageLayout',
        'CSSClassNames' => 'center',
    ],
    'sections' => [
        (object)[
            'className' => 'CBTextView2',
            'contentAsCommonMark' => $content,
            'CSSClassNames' => 'center',
        ],
    ],
]);

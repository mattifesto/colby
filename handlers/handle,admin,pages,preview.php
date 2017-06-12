<?php

if (!ColbyUser::current()->isOneOfThe('Administrators')) {
    return include CBSystemDirectory . '/handlers/handle-authorization-failed.php';
}

$ID = $_GET['ID'];
$version = empty($_GET['version']) ? null : $_GET['version'];

if (empty($version)) {
    $model = CBModels::fetchModelByID($ID);
} else {
    $model = CBModels::fetchModelByIDWithVersion($ID, $version);
}

$IDAsHTML = cbhtml($ID);

if ($model === false) {
    $HTML = <<<EOT

        <p>There is no model in the CBModels table for a page with this ID:
        <pre>{$IDAsHTML}</pre>
        <p>It's possible that this is an older page that hasn't yet moved
           its model to CBModels.

EOT;

    CBViewPage::renderModelAsHTML((object)[
        'titleHTML' => 'Page Preview Error',
        'sections' => [
            (object)[
                'className' => 'CBPageTitleAndDescriptionView',
            ],
            (object)[
                'className' => 'CBTextView2',
                'contentAsHTML' => $HTML,
                'CSSClassNames' => ['center'],
            ],
        ]

    ]);
} else {
    $function = "{$model->className}::renderModelAsHTML";

    if (is_callable($function)) {
        call_user_func($function, $model);
    } else {
        $functionAsHTML = cbhtml($function);
        $HTML = <<<EOT

            <p>The function:
            <pre>{$functionAsHTML}()</pre>
            <p>is not callable for the page with this ID:
            <pre>{$IDAsHTML}</pre>

EOT;

        CBViewPage::renderModelAsHTML((object)[
            'titleHTML' => 'Page Preview Error',
            'sections' => [
                (object)[
                    'className' => 'CBPageTitleAndDescriptionView',
                ],
                (object)[
                    'className' => 'CBTextView2',
                    'contentAsHTML' => $HTML,
                    'CSSClassNames' => ['center'],
                ],
            ]
        ]);
    }
}

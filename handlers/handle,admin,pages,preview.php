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
    $message = <<<EOT

        There is no model in the CBModels table for a page with this ID:

        --- pre preline
        {$IDAsHTML}
        ---

        It's possible that this is an older page that hasn't yet moved its model
        to CBModels.

EOT;

    CBPage::renderSpec((object)[
        'className' => 'CBViewPage',
        'title' => 'Page Preview Error',
        'layout' => (object)[
            'className' => 'CBPageLayout',
        ],
        'sections' => [
            (object)[
                'className' => 'CBPageTitleAndDescriptionView',
            ],
            (object)[
                'className' => 'CBMessageView',
                'markup' => $message,
            ],
        ]

    ]);
} else {
    CBPage::render($model);
}

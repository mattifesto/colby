<?php

if (
    !CBUserGroup::userIsMemberOfUserGroup(
        ColbyUser::getCurrentUserCBID(),
        'CBAdministratorsUserGroup'
    )
) {
    return include cbsysdir() . '/handlers/handle-authorization-failed.php';
}

$ID = cb_query_string_value('ID');
$version = cb_query_string_value('version');

if (empty($version)) {
    $model = CBModels::fetchModelByID($ID);
} else {
    $model = CBModels::fetchModelByIDWithVersion($ID, $version);
}

if ($model === false) {
    $IDAsHTML = CBMessageMarkup::stringToMessage($ID);

    $cbmessage = <<<EOT

        There is no model in the CBModels table for a page with the ID
        ({$IDAsHTML} (code)).

    EOT;

    $spec = CBModelTemplateCatalog::fetchLivePageTemplate();
    $spec->title = 'Page Preview Error';

    $spec->sections = [
        (object)[
            'className' => 'CBPageTitleAndDescriptionView',
        ],
        (object)[
            'className' => 'CBMessageView',
            'markup' => $cbmessage,
        ]
    ];

    CBPage::renderSpec($spec);
} else {
    CBPage::render($model);
}

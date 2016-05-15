<?php

final class CBWellKnownPageForTestingPageTitleAndBodyText {

    const ID = 'f42f10168d15e025bb99231f034b5bf319da7388';

    public static function install() {
        $spec = CBModels::fetchSpecByID(CBWellKnownPageForTestingPageTitleAndBodyText::ID);
        $originalSpec = clone $spec;

        if ($spec === false) {
            $spec = (object)[
                'ID' => CBWellKnownPageForTestingPageTitleAndBodyText::ID,
            ];
        }

        $spec->className = 'CBViewPage';
        $spec->title = 'Well-Known Page for Testing Page Title and Body Text';
        $spec->description = 'This page is used to test the formatting of CBPageTitleAndDescriptionView and CBThemedTextView.';
        $spec->isPublished = false;
        $spec->URI = null;

        include __DIR__ . '/sections.php';

        if ($spec != $originalSpec) {
            CBModels::save([$spec]);
        }
    }
}

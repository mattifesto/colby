<?php

final class CBWellKnownPageForTestingPageTitleAndBodyText {

    const ID = 'f42f10168d15e025bb99231f034b5bf319da7388';

    /**
     * @return null
     */
    static function install() {
        $originalSpec = CBModels::fetchSpecByID(CBWellKnownPageForTestingPageTitleAndBodyText::ID);

        if (empty($originalSpec)) {
            $spec = (object)[
                'ID' => CBWellKnownPageForTestingPageTitleAndBodyText::ID,
            ];
        } else {
            $spec = clone $originalSpec;
        }

        $spec->className = 'CBViewPage';
        $spec->description = 'This page is used to test the formatting of CBPageTitleAndDescriptionView and CBThemedTextView.';
        $spec->isPublished = false;
        $spec->title = 'Well-Known Page for Testing Page Title and Body Text';
        $spec->URI = null;

        if (empty($spec->publicationTimeStamp)) {
            $spec->publicationTimeStamp = time();
        }

        if (empty($spec->publishedBy)) {
            $spec->publishedBy = ColbyUser::currentUserId();
        }

        include __DIR__ . '/sections.php';

        if ($spec != $originalSpec) {
            CBModels::save([$spec]);
        }
    }
}

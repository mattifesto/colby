<?php

final class CBWellKnownPageForTestingCBTextView2 {

    const ID = 'ab9a674ce554ce49b7a1d1415f219f8fcf8a3e1f';

    /**
     * @return null
     */
    static function install() {
        $originalSpec = CBModels::fetchSpecByID(CBWellKnownPageForTestingCBTextView2::ID);

        if (empty($originalSpec)) {
            $spec = (object)[
                'ID' => CBWellKnownPageForTestingCBTextView2::ID,
            ];
        } else {
            $spec = clone $originalSpec;
        }

        $spec->className = 'CBViewPage';
        $spec->description = 'A page for testing and experimenting with CBTextView2 and CBTextView2StandardLayout.';
        $spec->isPublished = false;
        $spec->title = 'Well-Known Page for Testing CBTextView2';
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

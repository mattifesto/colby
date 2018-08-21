<?php

final class CBViewPageTests {

    static function CBTests_classTest() {

        /* Test 1 */

        $spec = (object)[
            'className' => 'CBViewPage',
            'classNameForSettings' => 'CBViewPageTests_PageSettings',
            'image' => (object)[
                /* testing deprecated missing class name */
                'ID' => 'bf0c7e133bf1a4bd05a3490a6c05d8fa34f5833f',
                'base' => 'original',
                'extension' => 'jpeg',
                'height' => 700,
                'width' => 900,
            ],
            'sections' => CBViewTests::testSubviewSpecs(),
        ];

        $expectedModel = (object)[
            'className' => 'CBViewPage',
            'classNameForKind' => '',
            'classNameForSettings' => 'CBViewPageTests_PageSettings',
            'description' => '',
            'frameClassName' => '',
            'isPublished' => false,
            'iteration' => 0,
            'publishedBy' => null,
            'selectedMenuItemNames' => [],
            'title' => '',
            'URI' => '',
            'publicationTimeStamp' => null,
            'thumbnailURL' => '',
            'layout' => null,
            'sections' => CBViewTests::testSubviewModels(),
            'thumbnailURLAsHTML' => '',
            'URIAsHTML' => '',
        ];

        $model = CBModel::build($spec);

        if ($model != $expectedModel) {
            return (object)[
                'message' =>
                    "Test 1: The result built model does not match the expected built model.\n\n" .
                    CBConvertTests::resultAndExpectedToMessage($model, $expectedModel),
            ];
        }

        /* Test 2 */

        $searchText = CBModel::toSearchText($model);
        $expectedSearchText = CBViewTests::testSubviewSearchText() . ' CBViewPage';

        if ($searchText !== $expectedSearchText) {
            return (object)[
                'message' =>
                    "Test 2: The result search text does not match the expected search text.\n\n" .
                    CBConvertTests::resultAndExpectedToMessage($searchText, $expectedSearchText),
            ];
        }

        /* Test 3 */

        $upgradedSpec = CBModel::upgrade($spec);
        $expectedUpgradedSpec = (object)[
            'className' => 'CBViewPage',
            'classNameForSettings' => 'CBViewPageTests_PageSettings',
            'image' => (object)[
                'className' => 'CBImage',
                'ID' => 'bf0c7e133bf1a4bd05a3490a6c05d8fa34f5833f',
                'filename' => 'original',
                'extension' => 'jpeg',
                'height' => 700,
                'width' => 900,
            ],
            'sections' => CBViewTests::testSubviewUpgradedSpecs(),
        ];

        if ($upgradedSpec != $expectedUpgradedSpec) {
            return (object)[
                'message' =>
                    "Test 3: The result upgraded spec does not match the expected upgraded spec.\n\n" .
                    CBConvertTests::resultAndExpectedToMessage($upgradedSpec, $expectedUpgradedSpec),
            ];
        }
    }

    /**
     * @return null
     */
    static function saveTest() {
        $ID = '697f4e4cb46436f5c204e495caff5957d4d62a31';
        $kind = 'CBViewPageTestPages';
        $specURI = 'CBViewPageTests/super écali fragil isticø expialidociouså';
        $modelURI = 'cbviewpagetests/super-cali-fragil-istic-expialidocious';

        /**
         * @NOTE The building emoji will cause an error if the table is not
         * correctly updated. A heart emoji will not cause an error.
         */

        $title = 'I 🏛 <Websites>!';

        CBModels::deleteByID($ID);

        $spec = (object)[
            'className' => 'CBViewPage',
            'ID' => $ID,
            'title' => $title,
            'classNameForKind' => $kind,
            'isPublished' => true,
            'URI' => $specURI,
        ];

        CBModels::save($spec);

        $count = CBDB::SQLToValue("SELECT COUNT(*) FROM `ColbyPages` WHERE `archiveID` = UNHEX('{$ID}')");

        if ($count != 1) {
            throw new Exception('The page was not found when searching by `ID`.');
        }

        $count = CBDB::SQLToValue("SELECT COUNT(*) FROM `ColbyPages` WHERE `classNameForKind` = '{$kind}'");

        if ($count != 1) {
            throw new Exception('The page was not found when searching by `classNameForKind`.');
        }

        $URI = CBDB::SQLToValue("SELECT `URI` FROM `ColbyPages` WHERE `archiveID` = UNHEX('{$ID}')");

        if ($URI != $modelURI) {
            $pu = json_encode($URI);
            $su = json_encode($spec->URI);
            throw new Exception("The page URI: {$pu} does not match the spec URI: {$su}.");
        }

        CBModels::deleteByID([$ID]);
    }
}

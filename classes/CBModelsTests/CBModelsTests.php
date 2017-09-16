<?php

final class CBModelsTests {

    const testModelIDs = [
        'dbc96d7b92337a3c6b9274c89473ee547ee518d7',
        '728038c2af4e49fb265c82971ac441f96d591056',
        '95397735c2fd6d7e75a2d059fededae693665f50'
    ];

    /**
     * This function assumes that the CBModels functionality is working. The
     * functions that call it will be testing to see if that is actually true.
     *
     * A transaction should be started before this function is called.
     *
     * @return null
     */
    private static function createTestEnvironment() {
        $specs = CBModels::fetchSpecsByID(CBModelsTests::testModelIDs, [
            'createSpecForIDCallback' => function($ID) {
                $spec           = CBModels::modelWithClassName('CBModelsTests_TestClass', ['ID' => $ID]);
                $spec->title    = "Title for {$ID}";
                $spec->name     = "Name {$ID}";
                return $spec;
            }
        ]);

        CBModels::save(array_values($specs));
    }

    /**
     * @return null
     */
    static function fetchModelByIDTest() {
        Colby::query('START TRANSACTION');
        CBModelsTests::createTestEnvironment();

        /* 1 */

        $ID     = CBModelsTests::testModelIDs[2];
        $model  = CBModels::fetchModelByID($ID);

        CBModelsTests_TestClass::checkModelWithID($model, $ID, 1);

        /* 2 */

        $ID     = CBHex160::random();
        $model  = CBModels::fetchModelByID($ID);

        if ($model !== false) {
            throw new Exception(__METHOD__ . ' Calling `CBModel::fetchModelByID` with and ID with no model should return false.');
        }

        Colby::query('ROLLBACK');
    }

    /**
     * @return null
     */
    static function fetchModelsByIDTest() {
        Colby::query('START TRANSACTION');
        CBModelsTests::createTestEnvironment();

        /* 1 */

        $models = CBModels::fetchModelsByID(CBModelsTests::testModelIDs);

        if (count($models) !== 3) {
            throw new Exception(__METHOD__ . ' Test 1: Array should contain 3 models.');
        }

        for ($i = 0; $i < 3; $i++) {
            $ID = CBModelsTests::testModelIDs[$i];
            CBModelsTests_TestClass::checkModelWithID($models[$ID], $ID, 1);
        }

        /* 2 */

        $IDs    = [CBHex160::random(), CBHex160::random(), CBHex160::random()];
        $models = CBModels::fetchModelsByID($IDs);

        if ($models !== []) {
            throw new Exception(__METHOD__ . ' Test 2: Array should be empty.');
        }

        /* 3 */

        $IDs    = [CBHex160::random(), CBHex160::random(), CBHex160::random()];
        $IDs    = array_merge(CBModelsTests::testModelIDs, $IDs);
        $models = CBModels::fetchModelsByID($IDs);

        if (count($models) !== 3) {
            throw new Exception(__METHOD__ . ' Test 3: Array should contain 3 models.');
        }

        Colby::query('ROLLBACK');
    }

    /**
     * CBModel::toModel() is allowed to return null in rare cases where a spec
     * doesn't have required properties. In general, spec shouldn't have
     * required properties, but in cases like CBImage they do. This function
     * tests the behavior of saving a spec that will generate a null model.
     */
    static function saveNullableModelTest() {
        try {
            Colby::query('START TRANSACTION');

            $spec = (object)[
                'ID' => CBHex160::random(),
                'className' => 'CBImage',
            ];

            CBModels::save([$spec]);

            Colby::query('ROLLBACK');
        } catch (Throwable $throwable) {
            Colby::query('ROLLBACK');

            $message = $throwable->getMessage();

            if ($message !== 'A spec being saved generated a null model.') {
                throw $throwable;
            }
        }
    }

    /**
     * CBModel::toModel() is allowed to return a model without an ID, however
     * these models cannot be saved.
     */
    static function saveSpecWithoutIDTest() {
        try {
            Colby::query('START TRANSACTION');

            $spec = (object)[
                'className' => 'CBViewPage',
            ];

            CBModels::save([$spec]);

            Colby::query('ROLLBACK');
        } catch (Throwable $throwable) {
            Colby::query('ROLLBACK');

            $message = $throwable->getMessage();

            if ($message !== 'A spec being saved generated a model without an ID.') {
                throw $throwable;
            }
        }
    }
}


final class CBModelsTests_TestClass {

    /**
     * Checks a model of this class.
     *
     * @return null
     */
    static function checkModelWithID(stdClass $model, $ID, $version = false) {
        if ($model->ID !== $ID) {
            throw new Exception(__METHOD__ . ' Incorrect model ID');
        }

        if ($model->className !== __CLASS__) {
            throw new Exception(__METHOD__ . ' Incorrect `className` property');
        }

        if (!isset($model->created) || !is_int($model->created)) {
            throw new Exception(__METHOD__ . ' Incorrect `created` property');
        }

        if (!isset($model->modified) || !is_int($model->modified)) {
            throw new Exception(__METHOD__ . ' Incorrect `modified` property');
        }

        if ($model->name !== "Name {$ID}") {
            throw new Exception(__METHOD__ . ' Incorrect `name` property');
        }

        if ($model->nameAsHTML !== "Name {$ID}") {
            throw new Exception(__METHOD__ . ' Incorrect `nameAsHTML` property');
        }

        if ($model->title !== "Title for {$ID}") {
            throw new Exception(__METHOD__ . ' The `title` property is not correct.');
        }

        if ($version !== false && $model->version !== $version) {
            $actual     = json_encode($model->version);
            $expected   = json_encode($version);
            throw new Exception(__METHOD__ . " Model version: {$actual}, Expected version: {$expected}");
        }
    }

    /**
     * @param object $spec
     *
     * @return object
     */
    static function CBModel_toModel(stdClass $spec) {
        $model = (object)[
            'className' => __CLASS__,
        ];
        $model->name = isset($spec->name) ? (string)$spec->name : '';
        $model->nameAsHTML = ColbyConvert::textToHTML($model->name);

        return $model;
    }
}

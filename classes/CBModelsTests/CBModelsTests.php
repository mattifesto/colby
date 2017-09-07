<?php

final class CBModelsTests {

    const testModelIDs = [
        'dbc96d7b92337a3c6b9274c89473ee547ee518d7',
        '728038c2af4e49fb265c82971ac441f96d591056',
        '95397735c2fd6d7e75a2d059fededae693665f50'
    ];

    /**
     * @return null
     */
    private static function checkSQL(array $checks) {
        array_walk($checks, function(array $check) {
            $value = CBDB::SQLToValue($check[0]);
            if ($value !== $check[1]) {
                $actual     = json_encode($value);
                $expected   = json_encode($check[1]);
                throw new Exception("Check returned {$actual} instead of {$expected}: {$check[0]}");
            }
        });
    }

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
     * Tests the behavior of making a version for a new model
     */
    static function makeVersionsForUpdateTest() {
        $ID0 = '3222378291fdbc3b958f95c69e0233cda61cc0a8';

        Colby::query('START TRANSACTION');

        $versions = CBModels::makeVersionsForUpdate([$ID0]);

        if ($versions[$ID0] !== null) {
            throw new Exception('The version returned for a new model is not correct.');
        }

        $IDAsSQL    = CBHex160::toSQL($ID0);
        $count      = CBDB::SQLToValue("SELECT COUNT(*) FROM `CBModels` WHERE `ID` = {$IDAsSQL} AND `version` = 0");

        if ($count !== '1') {
            throw new Exception('A new model should exist in the `CBModels` table with a verison of 0.');
        }

        CBModels::cancelUpdate();
        Colby::query('ROLLBACK');

        $count = CBDB::SQLToValue("SELECT COUNT(*) FROM `CBModels` WHERE `ID` = {$IDAsSQL}");

        if ($count !== '0') {
            throw new Exception('A new model should be removed from the `CBModels` if the update is not completed.');
        }
    }

    /**
     * Tests the behavior of making a spec for a new model
     */
    static function makeSpecsForUpdateTest() {
        Colby::query('START TRANSACTION');

        $ID     = 'a5db2923b26175ef624aacbd3e4fe344bc317486';
        $specs  = CBModels::makeSpecsForUpdate([$ID]);

        if ($specs[$ID]->ID !== $ID) {
            throw new Exception('The spec returned for a new model has the wrong ID.');
        }

        $IDAsSQL    = CBHex160::toSQL($ID);
        $count      = CBDB::SQLToValue("SELECT COUNT(*) FROM `CBModels` WHERE `ID` = {$IDAsSQL} AND `version` = 0");

        if ($count !== '1') {
            throw new Exception('A new model should exist in the `CBModels` table with a verison of 0.');
        }

        CBModels::cancelUpdate();
        Colby::query('ROLLBACK');

        $count = CBDB::SQLToValue("SELECT COUNT(*) FROM `CBModels` WHERE `ID` = {$IDAsSQL}");

        if ($count !== '0') {
            throw new Exception('A new model should be removed from the `CBModels` if the update is not completed.');
        }
    }

    /**
     * @return null
     */
    static function makeCustomSpecsForUpdateTest() {
        Colby::query('START TRANSACTION');

        $ID     = '38ab6bc56796350379e0e579c38e95a97d7669a8';
        $specs  = CBModels::makeSpecsForUpdate([$ID], function($ID) {
            return (object)['className' => 'CBModelsTests', 'ID' => $ID];
        });

        if ($specs[$ID]->ID != $ID || $specs[$ID]->className != 'CBModelsTests') {
            throw new Exception('The custom spec was not created propertly.');
        }

        CBModels::cancelUpdate();
        Colby::query('ROLLBACK');
    }

    /**
     * @return null
     */
    static function updateAndFetchTest() {
        Colby::query('START TRANSACTION');

        $ID                 = 'cc347c84af65b626520b6961612e5e6e8be7c8aa';
        $IDAsSQL            = CBHex160::toSQL($ID);
        $specs              = CBModels::makeSpecsForUpdate([$ID]);
        $spec               = $specs[$ID];
        $spec->className    = __CLASS__;
        $spec->title        = 'Hello, world!';
        $model              = CBModel::specToModel($spec);

        CBModels::updateModels([(object)['spec' => $spec, 'model' => $model]]);

        CBModelsTests::checkSQL([
            ["SELECT COUNT(*) FROM `CBModels` WHERE `ID` = {$IDAsSQL} AND `version` = 1", '1'],
            ["SELECT COUNT(*) FROM `CBModelVersions` WHERE `ID` = {$IDAsSQL}", '1']
        ]);


        $specs              = CBModels::makeSpecsForUpdate([$ID]);
        $spec               = $specs[$ID];
        $spec->title        = 'Mama Mia!';
        $model              = CBModel::specToModel($spec);

        CBModels::updateModels([(object)['spec' => $spec, 'model' => $model]]);

        CBModelsTests::checkSQL([
            ["SELECT COUNT(*) FROM `CBModels` WHERE `ID` = {$IDAsSQL} AND `version` = 2", '1'],
            ["SELECT COUNT(*) FROM `CBModelVersions` WHERE `ID` = {$IDAsSQL}", '2']
        ]);


        $specs              = CBModels::makeSpecsForUpdate([$ID]);
        $spec               = $specs[$ID];
        $spec->title        = 'Gimme! Gimme! Gimme!';
        $model              = CBModel::specToModel($spec);

        CBModels::updateModels([(object)['spec' => $spec, 'model' => $model]]);

        CBModelsTests::checkSQL([
            ["SELECT COUNT(*) FROM `CBModels` WHERE `ID` = {$IDAsSQL} AND `version` = 3", '1'],
            ["SELECT COUNT(*) FROM `CBModelVersions` WHERE `ID` = {$IDAsSQL}", '3']
        ]);

        $models = CBModels::fetchModels([$ID]);

        foreach ([
            ['$models[$ID]->version',   3],
            ['$models[$ID]->title',     'Gimme! Gimme! Gimme!']
        ] as $condition) {
            $actual     = eval("return {$condition[0]};");
            $expected   = $condition[1];
            if ($actual !== $expected) {
                $a = json_encode($actual);
                $e = json_encode($expected);
                throw new Exception("Expression evaluates to {$a} instead of {$e}: \"{$condition[0]}\"");
            }
        }

        $model = CBModels::fetchModelWithVersion($ID, 2);

        foreach ([
            ['$model->version', 2],
            ['$model->title',   'Mama Mia!']
        ] as $condition) {
            $actual     = eval("return {$condition[0]};");
            $expected   = $condition[1];
            if ($actual !== $expected) {
                $a = json_encode($actual);
                $e = json_encode($expected);
                throw new Exception("Expression evaluates to {$a} instead of {$e}: \"{$condition[0]}\"");
            }
        }

        Colby::query('ROLLBACK');
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

        if (!empty($model->title)) {
            throw new Exception(__METHOD__ . ' The `title` property should not be set.');
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
    static function specToModel(stdClass $spec) {
        $model = (object)[
            'className' => __CLASS__,
        ];
        $model->name = isset($spec->name) ? (string)$spec->name : '';
        $model->nameAsHTML = ColbyConvert::textToHTML($model->name);

        return $model;
    }
}
